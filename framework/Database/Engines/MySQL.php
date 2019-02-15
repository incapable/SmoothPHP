<?php

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
		self::checkError($this->connection);
	}

	public function commit() {
		$this->connection->commit();
		self::checkError($this->connection);
	}

	public function rollback() {
		$this->connection->rollback();
		self::checkError($this->connection);
	}

	private static function checkError($source = null) {
		if ($source->errno)
			throw new DatabaseException($source->error);
	}

	public function prepare($query) {
		$r = $this->connection->prepare($query);
		self::checkError($this->connection);
		return $r;
	}

	public function bindQueryParams($stmt, $params) {
		call_user_func_array([$stmt, 'bind_param'], $params);
		self::checkError($stmt);
	}
}