<?php

/**
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * **********
 * Copyright © 2015-2018
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * **********
 * MySQLStatementWithoutResult.php
 */

namespace SmoothPHP\Framework\Database\Statements;

use SmoothPHP\Framework\Database\MySQLException;

class MySQLStatementWithoutResult extends MySQLStatement {

	public function createResult() {
		$stmt = $this->getMySQLi_stmt();

		if ($stmt->errno)
			throw new MySQLException($stmt->error);

		$stmt->store_result();

		$id = $stmt->insert_id;

		$stmt->free_result();
		$stmt->reset();

		return $id;
	}

}
