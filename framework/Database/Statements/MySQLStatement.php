<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * MySQLStatement.php
 * (Potentially) prepared MySQL statement
 */

namespace SmoothPHP\Framework\Database\Statements;

use SmoothPHP\Framework\Database\MySQL;
use SmoothPHP\Framework\Database\MySQLException;
use SmoothPHP\Framework\Database\MySQLResult;

abstract class MySQLStatement {
	protected $mysql;

	protected $query;
	protected $params;
	protected $args;

	/* @var \mysqli_stmt */
	private $stmt;

	/**
	 * @param MySQL $mysql
	 * @param $query
	 * @throws MySQLException
	 */
	public function __construct(MySQL $mysql, $query) {
		$this->mysql = $mysql;
		$this->params = [''];
		$this->args = [];

		$previousMatch = null;
		$this->query = preg_replace_callback('/%(d|f|s|r)/', function (array $matches) use (&$previousMatch) {
			if ($matches[1] != 'r') {
				$this->params[0] .= $matches[1];
				$this->args[] = null;
				$previousMatch = $matches[1];
			} else {
				if ($previousMatch == null)
					throw new MySQLException('Trying to use %r (repeat) in a query with no previous variables.');
				$this->params[0] .= $previousMatch;
			}
			$this->params[] = &$this->args[count($this->args) - 1];
			return '?';
		}, $query);

		$this->verifyStmtAwake();
	}

	public function __sleep() {
		return ['mysql', 'query', 'params', 'args'];
	}

	/**
	 * @throws MySQLException
	 */
	private function verifyStmtAwake() {
		if (!isset($this->stmt)) {
			$this->mysql->__wakeup();
			$this->stmt = $this->mysql->getConnection()->prepare($this->query);
			MySQL::checkError($this->mysql->getConnection());

			if (count($this->params) > 1) {
				call_user_func_array([$this->stmt, 'bind_param'], $this->params);
				MySQL::checkError($this->stmt);
			}
		}
	}

	/**
	 * @throws MySQLException
	 */
	public function getMySQLi_stmt() {
		$this->verifyStmtAwake();
		return $this->stmt;
	}

	/**
	 * @return MySQLResult|int
	 * @throws MySQLException
	 */
	public function execute() {
		$this->verifyStmtAwake();

		$args = func_get_args();
		if (isset($args[0]) && is_array($args[0]))
			$args = $args[0];

		for ($i = 0; $i < count($args); $i++)
			$this->args[$i] = $args[$i];

		$this->stmt->execute();
		MySQL::checkError($this->stmt);

		return $this->createResult();
	}

	protected abstract function createResult();
}
