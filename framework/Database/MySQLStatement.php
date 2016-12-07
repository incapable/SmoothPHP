<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * MySQLStatement.php
 * (Potentially) prepared MySQL statement
 */

namespace SmoothPHP\Framework\Database;

abstract class MySQLStatement {
    protected $stmt;
    private $args;

    public function __construct(\mysqli $connection, $query) {
        $this->args = array();
        $params = array('');
        $query = preg_replace_callback('/%(d|f|s)/', function (array $matches) use (&$params) {
            $params[0] .= $matches[1];
            $this->args[] = null;
            $params[] = &$this->args[count($this->args) - 1];
            return '?';
        }, $query);

        $this->stmt = $connection->prepare($query);
        MySQL::checkError($connection);

        if (count($params) > 1) {
            call_user_func_array(array($this->stmt, 'bind_param'), $params);
            MySQL::checkError($this->stmt);
        }
    }

    public function getMySQLi_stmt() {
        return $this->stmt;
    }

    public function execute() {
        $args = func_get_args();
        if (is_array($args[0]))
            $args = $args[0];

        for ($i = 0; $i < count($args); $i++)
            $this->args[$i] = $args[$i];

        $this->stmt->execute();
        MySQL::checkError($this->stmt);

        return $this->createResult();
    }

    protected abstract function createResult();
}
