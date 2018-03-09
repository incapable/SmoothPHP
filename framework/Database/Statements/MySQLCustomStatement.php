<?php

/**
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * **********
 * Copyright © 2015-2018
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * **********
 * MySQLCustomStatement.php
 */

namespace SmoothPHP\Framework\Database\Statements;

class MySQLCustomStatement extends MySQLStatement {

	public function createResult() {
		return null;
	}

}