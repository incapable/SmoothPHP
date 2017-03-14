<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * AuthenticationManager.php
 * Main class for handling authentication.
 */

namespace SmoothPHP\Framework\Authentication;

use SmoothPHP\Framework\Authentication\UserTypes\AbstractUser;
use SmoothPHP\Framework\Authentication\UserTypes\AnonymousUser;
use SmoothPHP\Framework\Authentication\UserTypes\User;
use SmoothPHP\Framework\Core\Kernel;
use SmoothPHP\Framework\Database\Mapper\MySQLObjectMapper;
use SmoothPHP\Framework\Database\Statements\MySQLStatement;
use SmoothPHP\Framework\Flow\Requests\Request;
use SmoothPHP\Framework\Flow\Responses\RedirectResponse;
use SmoothPHP\Framework\Flow\Responses\Response;
use SmoothPHP\Framework\Forms\Constraints\MaximumLengthConstraint;
use SmoothPHP\Framework\Forms\Form;
use SmoothPHP\Framework\Forms\FormBuilder;
use SmoothPHP\Framework\Forms\Types as Types;

// A lot of security mechanics in this class were derived from StackOverflow
// http://stackoverflow.com/a/477578
class AuthenticationManager {

    const SESSION_KEY_LOGINTOKEN = 'sm_logintoken';

    // Login flow
    /* @var MySQLObjectMapper */
    private $loginSessionMap, $activeSessionMap, $userMap;
    /* @var MySQLStatement */
    private $permissionsQuery;
    /* @var Form */
    private $defaultForm;

    // Active user
    /* @var $user User|AbstractUser */
    private $user, $session, $permissions;

    public function initialize(Kernel $kernel) {
        $mysql = $kernel->getMySQL();
        $this->loginSessionMap = $mysql->map(LoginSession::class);
        $this->activeSessionMap = $mysql->map(ActiveSession::class);
        $this->userMap = $this->userMap ?: $mysql->map(User::class);
        $this->permissionsQuery = $mysql->prepare('
            SELECT `permission` FROM `permissions` WHERE `userId` = %d AND NOT ISNULL(`permission`)
            UNION DISTINCT
            SELECT `permission` FROM `permissions` WHERE `group` IN (SELECT `group` FROM `permissions` WHERE `userId` = %d AND NOT ISNULL(`group`)) AND NOT ISNULL(`permission`)
        ');

        $formBuilder = new FormBuilder();

        $formBuilder->add('_logintoken', Types\HiddenType::class);
        $formBuilder->add('email', Types\EmailType::class);
        $formBuilder->add('password', Types\PasswordType::class, array(
            'constraints' => array(
                new MaximumLengthConstraint(72) // For hashing we use BCRYPT, which is limited to 72 characters
            )
        ));
        $formBuilder->add('submit', Types\SubmitType::class);

        $this->defaultForm = $formBuilder->getForm();

        $this->permissions = array();
    }

    public function setUserClass($clazz) {
        $classDef = new \ReflectionClass($clazz);

        if (!$classDef->isSubclassOf(User::class))
            throw new \Exception('Class ' . $clazz . ' does not derive from ' . User::class);

        global $kernel;
        $this->userMap = $kernel->getMySQL()->map($clazz);
    }

    public function getLoginForm(Request $request) {
        // Do we have a known login session yet?
        $session = $this->loginSessionMap->fetch(array(
            '_separator' => 'OR', // Either match
            'token' => $request->post->_logintoken ?: (isset($_SESSION[self::SESSION_KEY_LOGINTOKEN]) ? $_SESSION[self::SESSION_KEY_LOGINTOKEN] : '-'),
            'ip' => $request->server->REMOTE_ADDR
        ));
        // If not, create a new login session
        if (!$session)
            $session = $this->assignLoginSession($request);

        $this->defaultForm->setValue('_logintoken', $session->getToken());

        return $this->defaultForm;
    }

    public function checkLoginResult(Request $request) {
        $form = $this->defaultForm;
        $form->validate($request);

        if ($form->hasResult() && $form->isValid()) {
            /* @var $session LoginSession */
            $session = $this->loginSessionMap->fetch(array(
                'token' => $request->post->_logintoken,
                'ip' => $request->server->REMOTE_ADDR
            ));

            if (!$session) {
                $form->addErrorMessage('Your session has expired, please try again.');
                $this->assignLoginSession($request);
                return false;
            }

            // Determine the timeout we enforce. 1 fail = 0, 2 fails = 2, 3 fails = 4, 4 fails = 8, >=5 fails = 16 sec
            $timeout = min($session->getFailedAttempts() < 2 ? 0 : pow(2, $session->getFailedAttempts() - 1), 16);
            $remaining = $timeout - (time() - $session->getLastUpdate());
            if ($remaining > 0) {
                $form->addErrorMessage('Please try again in ' . $remaining . ' seconds.');
                return false;
            }

            /* @var $user User */
            $user = $this->userMap->fetch(array(
                'email' => $request->post->get('email')
            ));

            if (!$user || !password_verify($request->post->password, $user->getHashedPassword())) {
                $form->addErrorMessage('Email and/or password are incorrect.');
                $session->increaseFailure();
                $this->loginSessionMap->insert($session);
                return false;
            }

            $this->loginSessionMap->delete($session);
            $activeSession = new ActiveSession($user);
            $this->activeSessionMap->insert($activeSession);

            return true;
        } else
            return false;
    }

    private function assignLoginSession(Request $request) {
        $session = new LoginSession($request);
        $this->loginSessionMap->insert($session);
        $_SESSION[self::SESSION_KEY_LOGINTOKEN] = $session->getToken();
        return $session;
    }

    /**
     * @return User|AbstractUser
     */
    public function getActiveUser() {
        if (!$this->user) {
            $this->session = ActiveSession::readCookie($this->activeSessionMap);
            if ($this->session == null)
                return new AnonymousUser();
            else {
                $this->user = $this->userMap->fetch($this->session->getUserId());
                $this->getPermissions($this->user);
            }
        }

        return $this->user;
    }

    /**
     * @param int|User $user
     * @return array
     */
    public function getPermissions($user) {
        if ($user instanceof User)
            $user = $user->getId();
        if (!isset($this->permissions[$user]))
            $this->permissions[$user] = $this->permissionsQuery->execute($user, $user)->getAsArray();
        return $this->permissions[$user];
    }

    public function hasPermission($permission, $user = false) {
        if (!$user)
            $user = $this->user;
        return in_array($permission, $this->getPermissions($user));
    }

    public function canGo() {
        $args = func_get_args();

        if (!isset($args[0]))
            throw new \RuntimeException('AuthenticationManager#canGo(...) called with no arguments, requires at least 1.');

        global $kernel;
        $route = $kernel->getRouteDatabase()->getRoute($args[0]);
        return $this->verifyAccess(null, $route, array_splice($args, 1));
    }

    public function verifyAccess(Request $request, array $routeOpts, array $parameters) {
        if (isset($routeOpts['authentication']) && $routeOpts['authentication'] !== false) {
            $user = $this->getActiveUser();

            // Plain boolean
            if ($routeOpts['authentication'] === true) {
                if (!$user->isLoggedIn())
                    return $request ? $this->determineNoAccessAction($request, false) : false;
            }

            // Callable function
            else if (is_callable($routeOpts['authentication'])) {
                $response = call_user_func($routeOpts['authentication'], $routeOpts, $parameters);
                if ($response instanceof Response)
                    return $request ? $response : false;
                else if ($response === false)
                    return $request ? $this->determineNoAccessAction($request, $user->isLoggedIn()) : false;
            }

            // Permissions
            else {
                $required = (array) $routeOpts['authentication'];
                $missing = array_diff($required, $this->permissions[$user->getId()]);
                if (count($missing) > 0)
                    return $request ? $this->determineNoAccessAction($request, $user->isLoggedIn()) : false;
            }
        }

        return true; // Proceed as normal
    }

    private function determineNoAccessAction(Request $request, $isLoggedIn) {
        global $kernel;
        if (!$isLoggedIn && isset($kernel->getConfig()->authentication_loginroute))
            return new RedirectResponse($kernel->getRouteDatabase()->buildPath($kernel->getConfig()->authentication_loginroute) . '?' . http_build_query(array(
                'ref' => $request->server->REQUEST_URI
                )));

        return $kernel->error($kernel->getLanguageRepository()->getEntry('smooth_error_access'));
    }

    public function logout() {
        $this->getActiveUser();
        if ($this->session)
            $this->activeSessionMap->delete($this->session);

        $domain = explode('.', $_SERVER['SERVER_NAME']);
        if (count($domain) < 2)
            $cookieDomain = $_SERVER['SERVER_NAME'];
        else
            $cookieDomain = sprintf('.%s.%s', $domain[count($domain) - 2], $domain[count($domain) - 1]);
        setcookie(ActiveSession::SESSION_KEY, '-', 1, '/', $cookieDomain, false, false);
    }

}