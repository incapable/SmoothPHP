<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * ActiveSession.php
 * An active session (post-login) which keeps track of an user that is logged in.
 */

namespace SmoothPHP\Framework\Authentication;

use SmoothPHP\Framework\Authentication\UserTypes\User;
use SmoothPHP\Framework\Database\Mapper\MappedMySQLObject;
use SmoothPHP\Framework\Database\Mapper\MySQLObjectMapper;

class ActiveSession extends MappedMySQLObject {

    const SESSION_KEY = 'sm_ases';

    private $userId;
    private $selector;
    private $validator;

    public function __construct(User $user) {
        $this->userId = $user->getId();
        $this->selector = bin2hex(openssl_random_pseudo_bytes(16));

        $validator = base64_encode(openssl_random_pseudo_bytes(64));
        $this->validator = hash('sha512', $validator, false);

        $domain = explode('.', $_SERVER['SERVER_NAME']);
        if (count($domain) < 2)
            $cookieDomain = $_SERVER['SERVER_NAME'];
        else
            $cookieDomain = sprintf('.%s.%s', $domain[count($domain) - 2], $domain[count($domain) - 1]);

        setcookie(self::SESSION_KEY, sprintf('%s:%s', $this->selector, $validator),
            0, // This cookie expires at the end of the session
            '/', // This cookie applies to all sub-paths
            $cookieDomain, // Apply it to this host and all its subdomains
            false, // This cookie does not require HTTPS
            false); // This cookie can be transferred over non-HTTP
    }

    public function getTableName() {
        return 'sessions';
    }

    public function getUserId() {
        return $this->userId;
    }

    public static function readCookie(MySQLObjectMapper $map) {
        if (isset($_COOKIE[self::SESSION_KEY])) {
            $cookie = explode(':', $_COOKIE[self::SESSION_KEY]);

            if (count($cookie) != 2)
                return null;

            /* @var $session ActiveSession */
            $session = $map->fetch(array(
                'selector' => $cookie[0]
            ));

            if (!$session)
                return null;

            if (!hash_equals($session->validator, hash('sha512', $cookie[1], false)))
                return null;

            return $session;
        }

        return null;
    }

}