<?php

/**
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * **********
 * Copyright © 2015-2019
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * **********
 * MySQL.php
 */

/**
 * PhpComposerExtensionStubsInspection -> This is a suggested dependency.
 * PhpUnhandledExceptionInspection -> Will be handled by wrappers.
 * @noinspection PhpComposerExtensionStubsInspection,PhpUnhandledExceptionInspection
 */

namespace SmoothPHP\Framework\Database\Engines;

use SmoothPHP\Framework\Core\Config;
use SmoothPHP\Framework\Database\DatabaseException;

class MySQL implements Engine {
	/* @var $connection \mysqli */
	private $connection;

	public function connect(Config $config) {
		$prefix = ini_get('mysqli.allow_persistent') ? 'p:' : '';
		$this->connection = new \mysqli($prefix . $config->db_host, $config->db_user, $config->db_password, $config->db_database);
		if (!$this->connection->ping())
			throw new DatabaseException('Could not ping nor reconnect MySQL server.');
		if (!$this->connection->real_query('SET SESSION sql_mode = \'\';'))
			throw new DatabaseException('Could not reset sql_mode for session: ' . $this->connection->error);
		if (!$this->connection->set_charset('utf8'))
			throw new DatabaseException('Could not set charset for MySQLi client: ' . $this->connection->error);
	}

	public function disconnect() {
		$this->connection->close();
	}

	public function getShortName() {
		return 'my';
	}

	public function start() {
		$this->connection->begin_transaction();
		mySQLCheckError($this->connection);
	}

	public function commit() {
		$this->connection->commit();
		mySQLCheckError($this->connection);
	}

	public function rollback() {
		$this->connection->rollback();
		mySQLCheckError($this->connection);
	}

	public function prepare($query, array &$args = [], array &$params = []) {
		$params[0] = '';
		$previousMatch = null;
		$query = preg_replace_callback('/\'[^\']*\'(*SKIP)(*FAIL)|%(d|f|s|r)/', function (array $matches) use (&$previousMatch, &$args, &$params) {
			if ($matches[1] != 'r') {
				$params[0] .= $matches[1];
				$args[] = null;
				$previousMatch = $matches[1];
			} else {
				if ($previousMatch == null)
					throw new DatabaseException('Trying to use %r (repeat) in a query with no previous variables.');
				$params[0] .= $previousMatch;
			}
			$params[] = &$args[count($args) - 1];
			return '?';
		}, $query);

		$stmt = $this->connection->prepare($query);
		mySQLCheckError($this->connection);

		if (count($params) > 1) {
			call_user_func_array([$stmt, 'bind_param'], $params);
			mySQLCheckError($stmt);
		}

		return new MySQLStatement($stmt);
	}

	public function quote($field) {
		return '`' . $field . '`';
	}

	public function wipe() {
		if (__ENV__ != 'cli')
			die('Wipe cannot be called outside of the CLI environment.');

		global $kernel;

		print('Dropping constraints...' . PHP_EOL);
		$constraints = $this->connection->query("SELECT DISTINCT
			    CONCAT('ALTER TABLE `',
			            K.TABLE_NAME,
			            '` DROP FOREIGN KEY `',
			            K.CONSTRAINT_NAME,
			            '`;') AS query
			FROM
			    information_schema.KEY_COLUMN_USAGE K
			        LEFT JOIN
			    information_schema.REFERENTIAL_CONSTRAINTS C USING (CONSTRAINT_NAME)
			WHERE
			    K.REFERENCED_TABLE_SCHEMA = '" . $this->connection->escape_string($kernel->getConfig()->db_database) . "'");
		mySQLCheckError($this->connection);

		while ($constraint = $constraints->fetch_assoc()) {
			printf('Executing: %s' . PHP_EOL, $constraints->query);
			$this->connection->real_query($constraint['query']);
			mySQLCheckError($this->connection);
		}
		$constraints->free_result();

		print('Dropping tables...' . PHP_EOL);
		$databases = $this->connection->query("
			SELECT
			    concat('DROP TABLE IF EXISTS `', table_name, '`;') AS query
			FROM
			    information_schema.tables
			WHERE
			    table_schema = '" . $this->connection->escape_string($kernel->getConfig()->db_database) . "'");
		mySQLCheckError($this->connection);

		while ($database = $databases->fetch_assoc()) {
			printf('Executing: %s' . PHP_EOL, $database['query']);
			$this->connection->real_query($database['query']);
			mySQLCheckError($this->connection);
		}
	}
}

class MySQLStatement implements Statement {
	private $stmt;

	public function __construct(\mysqli_stmt $stmt) {
		$this->stmt = $stmt;
	}

	public function execute() {
		$this->stmt->execute();
	}

	public function getInsertID() {
		$id = $this->stmt->insert_id;
		$this->stmt->reset();
		return $id;
	}

	public function getResults() {
		$resultList = [];

		$result = $this->stmt->get_result();
		mySQLCheckError($this->stmt);

		if ($result->num_rows > 0)
			while ($data = $result->fetch_assoc())
				$resultList[] = $data;

		$this->stmt->free_result();
		$this->stmt->reset();
		return $resultList;
	}
}

function mySQLCheckError($source = null) {
	if ($source->errno)
		throw new DatabaseException($source->error);
}