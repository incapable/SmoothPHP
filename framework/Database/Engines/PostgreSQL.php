<?php

/**
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * **********
 * Copyright © 2015-2019
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * **********
 * PostgreSQL.php
 */

/**
 * PhpComposerExtensionStubsInspection -> This is a suggested dependency.
 * PhpUnhandledExceptionInspection -> Will be handled by wrappers.
 * @noinspection PhpComposerExtensionStubsInspection,PhpUnhandledExceptionInspection
 */

namespace SmoothPHP\Framework\Database\Engines;

use SmoothPHP\Framework\Core\Config;
use SmoothPHP\Framework\Database\DatabaseException;

class PostgreSQL implements Engine {
	private $connection;

	public function connect(Config $config) {
		$this->connection = \pg_connect(sprintf('host=%s port=%d dbname=%s user=%s password=%s %s',
			$config->db_host,
			$config->db_port,
			$config->db_database,
			$config->db_user,
			$config->db_password,
			$config->db_parameters));
		if (!$this->connection)
			throw new DatabaseException(\pg_last_error($this->connection));
	}

	public function disconnect() {
		if (!\pg_close($this->connection))
			throw new DatabaseException(\pg_last_error($this->connection));
	}

	public function getShortName() {
		return 'pg';
	}

	public function start() {
		if (!\pg_query($this->connection, "BEGIN"))
			throw new DatabaseException(\pg_last_error($this->connection));
	}

	public function commit() {
		if (!\pg_query($this->connection, "COMMIT"))
			throw new DatabaseException(\pg_last_error($this->connection));
	}

	public function rollback() {
		if (!\pg_query($this->connection, "ROLLBACK"))
			throw new DatabaseException(\pg_last_error($this->connection));
	}

	public function prepare($query, array &$args = [], array &$params = []) {
		$previousMatch = null;
		$query = preg_replace_callback('/\'[^\']*\'(*SKIP)(*FAIL)|%(d|f|s|r)/', function (array $matches) use (&$previousMatch, &$args, &$params) {
			if ($matches[1] != 'r') {
				$args[] = null;
				$previousMatch = $matches[1];
				$params[] = &$args[count($args) - 1];
			} else if ($previousMatch == null)
				throw new DatabaseException('Trying to use %r (repeat) in a query with no previous variables.');

			if ($matches[1] == 's')
				return 'NULLIF($' . count($params) . ', \'\')';

			return '$' . count($params);
		}, $query);

		$stmtName = "sphp_pgprep_" . md5($query);
		try {
			if (!\pg_prepare($this->connection, $stmtName, $query))
				throw new DatabaseException(\pg_last_error($this->connection));
		} catch (\ErrorException $e) {
			if (strpos($e->getMessage(), 'already exists') === false)
				throw $e;
		}

		return new PostgreSQLStatement($this->connection, $stmtName, $params);
	}

	public function quote($field) {
		return '"' . $field . '"';
	}

	public function wipe() {
		if (__ENV__ != 'cli')
			die('Wipe cannot be called outside of the CLI environment.');

		if (!pg_query($this->connection, 'DROP OWNED BY current_user CASCADE;'))
			throw new DatabaseException(\pg_last_error($this->connection));
	}
}

class PostgreSQLStatement implements Statement {
	private $connection;
	private $stmtName;
	private $params;

	private $result;

	public function __construct($connection, $stmtName, array &$params) {
		$this->connection = $connection;
		$this->stmtName = $stmtName;
		$this->params = &$params;
	}

	public function execute() {
		$this->result = \pg_execute($this->connection, $this->stmtName, $this->params);
		if (!$this->result)
			throw new DatabaseException(\pg_last_error($this->connection));
	}

	public function getInsertID() {
		$results = \pg_fetch_all($this->result);
		if (!$results) {
			$err = \pg_last_error($this->connection);
			if ($err)
				throw new DatabaseException($err);
			else
				return 0;
		}

		\pg_free_result($this->result);
		return $results[0]['id'];
	}

	public function getResults() {
		if (pg_num_rows($this->result) == 0)
			return [];

		$resultList = \pg_fetch_all($this->result);
		if (!$resultList)
			throw new DatabaseException(\pg_last_error($this->connection));
		\pg_free_result($this->result);
		return $resultList;
	}
}