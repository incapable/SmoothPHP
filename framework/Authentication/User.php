<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * User.php
 * Default user implementation
 */

namespace SmoothPHP\Framework\Authentication;

use SmoothPHP\Framework\Database\Mapper\MappedMySQLObject;

class User extends MappedMySQLObject {

    private $username;
    private $password;
    private $email;

    public function getTableName() {
        return 'users';
    }

    public function getHashedPassword() {
        return $this->password;
    }

}