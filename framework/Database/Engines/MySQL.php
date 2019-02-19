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

	public function prepare($query, array &$params = []) {
		$stmt = $this->connection->prepare($query);
		mySQLCheckError($this->connection);

		if (count($params) > 1) {
			call_user_func_array([$stmt, 'bind_param'], $params);
			mySQLCheckError($stmt);
		}

		return new MySQLStatement($stmt);
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