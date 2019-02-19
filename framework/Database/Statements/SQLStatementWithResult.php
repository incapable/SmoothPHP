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

use SmoothPHP\Framework\Database\DatabaseResult;

class SQLStatementWithResult extends SQLStatement {

	public function createResult() {
		return new DatabaseResult($this->getStatement()->getResults());
	}

}