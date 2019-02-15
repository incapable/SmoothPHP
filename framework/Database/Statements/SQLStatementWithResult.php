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

use SmoothPHP\Framework\Database\Database;
use SmoothPHP\Framework\Database\DatabaseResult;

class SQLStatementWithResult extends SQLStatement {

	public function createResult() {
		$resultList = [];
		$stmt = $this->getMySQLi_stmt();

		$result = $stmt->get_result();
		Database::checkError($stmt);

		if ($result->num_rows > 0)
			while ($data = $result->fetch_assoc())
				$resultList[] = $data;

		$stmt->free_result();
		$stmt->reset();

		return new DatabaseResult($resultList);
	}

}