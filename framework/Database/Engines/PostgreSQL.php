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

use PDO;
use PDOException;
use PDOStatement;
use SmoothPHP\Framework\Core\Config;
use SmoothPHP\Framework\Database\DatabaseException;

class PostgreSQL implements Engine {
	/* @var $connection PDO */
	private $connection;

	public function connect(Config $config) {
		try {
			$this->connection = new PDO(sprintf('pgsql:host=%s port=%d dbname=%s user=%s password=%s %s',
				$config->db_host,
				$config->db_port,
				$config->db_database,
				$config->db_user,
				$config->db_password,
				$config->db_parameters));
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			throw new DatabaseException($e);
		}
	}

	public function getShortName() {
		return 'pg';
	}

	public function start() {
		try {
			$this->connection->beginTransaction();
		} catch (PDOException $e) {
			throw new DatabaseException($e);
		}
	}

	public function commit() {
		try {
			$this->connection->commit();
		} catch (PDOException $e) {
			throw new DatabaseException($e);
		}
	}

	public function rollback() {
		try {
			$this->connection->rollBack();
		} catch (PDOException $e) {
			throw new DatabaseException($e);
		}
	}

	public function prepare($query, array &$args = [], array &$params = []) {
		$previousMatch = null;
		$query = preg_replace_callback('/\'[^\']*\'(*SKIP)(*FAIL)|%(d|f|s|r)/', function (array $matches) use (&$previousMatch, &$args, &$params) {
			if ($matches[1] != 'r') {
				$args[] = null;
				$previousMatch = $matches[1];
			} else if ($previousMatch == null)
				throw new DatabaseException('Trying to use %r (repeat) in a query with no previous variables.');

			$params[] = &$args[count($args) - 1];
			return '?';
		}, $query);

		try {
			$stmt = $this->connection->prepare($query);
			return new PostgreSQLStatement($stmt, $params);
		} catch (PDOException $e) {
			throw new DatabaseException($e);
		}
	}

	public function quote($field) {
		return '"' . $field . '"';
	}

	public function wipe() {
		if (__ENV__ != 'cli')
			die('Wipe cannot be called outside of the CLI environment.');

		try {
			$this->connection->exec('DROP OWNED BY current_user CASCADE');
		} catch (PDOException $e) {
			throw new DatabaseException($e);
		}
	}
}

class PostgreSQLStatement implements Statement {
	private $stmt;
	private $params;

	public function __construct(PDOStatement $stmt, array &$params) {
		$this->stmt = $stmt;
		$this->params = &$params;
	}

	public function execute() {
		try {
			$this->stmt->execute($this->params);
		} catch (PDOException $e) {
			throw new DatabaseException($e);
		}
	}

	public function getInsertID() {
		try {
			$results = $this->stmt->fetchAll();

			if (!isset($results[0]['id']))
				return 0;

			return $results[0]['id'];
		} catch (PDOException $e) {
			throw new DatabaseException($e);
		}
	}

	public function getResults() {
		try {
			return $this->stmt->fetchAll();
		} catch (PDOException $e) {
			throw new DatabaseException($e);
		}
	}
}