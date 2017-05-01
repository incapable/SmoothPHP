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
use SmoothPHP\Framework\Database\MySQLResult;

abstract class MySQLStatement {
	protected $mysql;

	protected $query;
	protected $params;
	protected $args;

	/* @var \mysqli_stmt */
	private $stmt;

	public function __construct(MySQL $mysql, $query) {
		$this->mysql = $mysql;
		$this->params = [''];
		$this->args = [];

		$this->query = preg_replace_callback('/%(d|f|s)/', function (array $matches) {
			$this->params[0] .= $matches[1];
			$this->args[] = null;
			$this->params[] = &$this->args[count($this->args) - 1];
			return '?';
		}, $query);

		$this->verifyStmtAwake();
	}

	public function __sleep() {
		return ['mysql', 'query', 'params', 'args'];
	}

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

	public function getMySQLi_stmt() {
		$this->verifyStmtAwake();
		return $this->stmt;
	}

	/**
	 * @return MySQLResult|int
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
