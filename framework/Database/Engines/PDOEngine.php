<?php

/**
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * **********
 * Copyright © 2015-2019
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * **********
 * PDOEngine.php
 */

namespace SmoothPHP\Framework\Database\Engines;

use PDO;
use PDOException;
use PDOStatement;
use SmoothPHP\Framework\Core\Config;
use SmoothPHP\Framework\Database\DatabaseException;

abstract class PDOEngine implements Engine {
	/* @var $connection PDO */
	protected $connection;

	protected abstract function getDSN(Config $config);

	public function connect(Config $config) {
		try {
			$this->connection = new PDO($this->getDSN($config), $config->db_user, $config->db_password);
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			throw new DatabaseException($e);
		}
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
			return $this->createEngineStatement($stmt, $params);
		} catch (PDOException $e) {
			throw new DatabaseException($e);
		}
	}

	protected abstract function createEngineStatement(PDOStatement $stmt, array &$params);

}

abstract class PDOSQLStatement implements Statement {
	protected $stmt;
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

	public abstract function getInsertID();

	public function getResults() {
		try {
			return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			throw new DatabaseException($e);
		}
	}
}