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

use SmoothPHP\Framework\Authentication\UserTypes\AnonymousUser;
use SmoothPHP\Framework\Authentication\UserTypes\User;
use SmoothPHP\Framework\Core\Kernel;
use SmoothPHP\Framework\Database\Mapper\MySQLObjectMapper;
use SmoothPHP\Framework\Flow\Requests\Request;
use SmoothPHP\Framework\Forms\Constraints\MaximumLengthConstraint;
use SmoothPHP\Framework\Forms\FormBuilder;
use SmoothPHP\Framework\Forms\Types as Types;

// A lot of security mechanics in this class were derived from StackOverflow
// http://stackoverflow.com/a/477578
class AuthenticationManager {

    const SESSION_KEY_LOGINTOKEN = 'sm_logintoken';

    /* @var MySQLObjectMapper */
    private $loginSessionMap, $activeSessionMap, $userMap;
    private $defaultForm;

    public function __construct(Kernel $kernel) {
        $this->loginSessionMap = $kernel->getMySQL()->map(LoginSession::class);
        $this->activeSessionMap = $kernel->getMySQL()->map(ActiveSession::class);
        $this->userMap = $kernel->getMySQL()->map(User::class);

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

        $this->defaultForm->inputs->_logintoken->input->setValue($session->getToken());

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
                $form->addErrorMessage('Username and/or password are incorrect.');
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

    public function getActiveUser() {
        $session = ActiveSession::readCookie($this->activeSessionMap);
        if ($session == null)
            return new AnonymousUser();
        else
            return $this->userMap->fetch($session->getUserId());
    }

}