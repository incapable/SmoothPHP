<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * MySQLStatementWithResult.php
 * Description
 */

namespace SmoothPHP\Framework\Database;

class MySQLStatementWithResult extends MySQLStatement {
    private $results;

    public function __construct(\mysqli $connection, $query) {
        parent::__construct($connection, $query);
        $this->results = array();

        $references = array();
        foreach ($this->stmt->result_metadata()->fetch_fields() as $field) {
            $this->results[$field->name] = null;
            $references[] = &$this->results[$field->name];
        }

        call_user_func_array(array($this->stmt, 'bind_result'), $references);
    }

    public function createResult() {
        $resultList = array();

        while ($this->stmt->fetch()) {
            $resultList[] = array_map(function ($val) {
                return $val;
            }, $this->results);
        }

        $this->stmt->free_result();

        return new MySQLResult($resultList);
    }

}