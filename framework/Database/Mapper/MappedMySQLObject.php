<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * MappedMySQLObject.php
 * Superclass for all MySQL-mapped objects
 */

namespace SmoothPHP\Framework\Database\Mapper;

class MappedMySQLObject {
    protected $id = 0;

    public function getTableName() {
        return strtolower((new \ReflectionClass($this))->getShortName());
    }

    public function getId() {
        return $this->id;
    }
}