<?php

/**
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * **********
 * Copyright © 2015-2018
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * **********
 * MySQLStatementWithResult.php
 */

namespace SmoothPHP\Framework\Database\Statements;

use SmoothPHP\Framework\Database\MySQL;
use SmoothPHP\Framework\Database\MySQLResult;

class MySQLStatementWithResult extends MySQLStatement {

	public function createResult() {
		$resultList = [];
		$stmt = $this->getMySQLi_stmt();

		$result = $stmt->get_result();
		MySQL::checkError($result);

		if ($result->num_rows > 0)
			while ($data = $result->fetch_assoc())
				$resultList[] = $data;

		$stmt->free_result();
		$stmt->reset();

		return new MySQLResult($resultList);
	}

}