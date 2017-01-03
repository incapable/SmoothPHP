<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * MySQL.php
 * Main entry point for all MySQL connections
 */

namespace SmoothPHP\Framework\Database;

use SmoothPHP\Framework\Core\Config;
use SmoothPHP\Framework\Database\Mapper\MySQLObjectMapper;
use SmoothPHP\Framework\Database\Statements as Statements;

class MySQL {
    private $connection;
    private $maps;

    public function __construct(Config $config) {
        $prefix = ini_get('mysqli.allow_persistent') ? 'p:' : '';
        $this->connection = new \mysqli($prefix . $config->mysql_host, $config->mysql_user, $config->mysql_password, $config->mysql_database);
        $this->maps = array();
    }

    public function prepareCustom($query) {
        return new Statements\MySQLCustomStatement($this->connection, $query);
    }

    public function prepare($query, $returnsData = true) {
        return $returnsData ? new Statements\MySQLStatementWithResult($this->connection, $query)
            : new Statements\MySQLStatementWithoutResult($this->connection, $query);
    }

    public function start() {
        $this->connection->begin_transaction();
    }

    public function commit() {
        $this->connection->commit();
    }

    public function rollback() {
        $this->connection->rollback();
    }

    /**
     * @param $query
     * @param array $params
     * @return int Insert id if applicable, num_rows otherwise
     */
    public function execute($query, array $params = array()) {
        return $this->prepare($query, false)->execute($params);
    }

    /**
     * @param $query
     * @param array $params
     * @return MySQLResult
     */
    public function fetch($query, array $params = array()) {
        return $this->prepare($query, true)->execute($params);
    }

    /**
     * @param $clazz
     * @return MySQLObjectMapper
     */
    public function map($clazz) {
        if (__ENV__ == 'cli')
            return null;

        if (!isset($this->maps[$clazz]))
            $this->maps[$clazz] = new MySQLObjectMapper($this, $clazz);

        return $this->maps[$clazz];
    }

    public static function checkError($source) {
        if ($source->errno)
            throw new MySQLException($source->error);
    }

}
