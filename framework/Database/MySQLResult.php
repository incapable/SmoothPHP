<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * MySQLResult.php
 * Wrapper for MySQL result arrays
 */

namespace SmoothPHP\Framework\Database;

class MySQLResult {
    private $results;
    private $current;

    public function __construct(array $results) {
        $this->results = $results;
        $this->current = 0;
    }

    public function hasData() {
        return isset($this->results[$this->current]);
    }

    public function next() {
        if ($this->current >= count($this->results))
            return false;
        else {
            $this->current++;
            return true;
        }
    }

    public function __get($varName) {
        if (isset($this->results[$this->current][$varName]))
            return $this->results[$this->current][$varName];
        else
            return false;
    }
}