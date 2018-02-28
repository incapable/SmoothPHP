<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
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
	/* @var \mysqli */
	private $connection;
	private $config;
	private $maps;

	public function __construct(Config $config) {
		$this->config = $config;
		$this->__wakeup();
		$this->maps = [];
	}

	public function __sleep() {
		return ['config', 'maps'];
	}

	public function __wakeup() {
		if (!isset($this->connection)) {
			$prefix = ini_get('mysqli.allow_persistent') ? 'p:' : '';
			$this->connection = new \mysqli($prefix . $this->config->mysql_host, $this->config->mysql_user, $this->config->mysql_password, $this->config->mysql_database);
			if (!$this->connection->real_query('SET SESSION sql_mode = \'\';'))
				throw new MySQLException('Could not reset sql_mode for session: ' . $this->connection->error);
			if (!$this->connection->set_charset('utf8'))
				throw new MySQLException('Could not set charset for MySQLi client: ' . $this->connection->error);
		}
	}

	public function getConnection() {
		return $this->connection;
	}

	public function prepareCustom($query) {
		return new Statements\MySQLCustomStatement($this, $query);
	}

	public function prepare($query, $returnsData = true) {
		return $returnsData ? new Statements\MySQLStatementWithResult($this, $query)
			: new Statements\MySQLStatementWithoutResult($this, $query);
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
	public function execute($query, array $params = []) {
		return $this->prepare($query, false)->execute($params);
	}

	/**
	 * @param $query
	 * @param array $params
	 * @return MySQLResult
	 */
	public function fetch($query, array $params = []) {
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
