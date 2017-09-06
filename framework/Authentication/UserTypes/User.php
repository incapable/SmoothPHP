<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * User.php
 * Default user implementation
 */

namespace SmoothPHP\Framework\Authentication\UserTypes;

use SmoothPHP\Framework\Database\Mapper\MappedMySQLObject;

class User extends MappedMySQLObject implements AbstractUser {

	protected $email;
	protected $password;

	public function getTableName() {
		return 'users';
	}

	public function getHashedPassword() {
		return $this->password;
	}

	public function isLoggedIn() {
		return true;
	}

	public function __get($name) {
		return $this->{$name};
	}

	public static function getInstance() {
		global $kernel;
		return $kernel->getAuthenticationManager()->getActiveUser();
	}

}