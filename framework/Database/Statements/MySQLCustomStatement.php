<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * MySQLCustomStatement.php
 * Prepared MySQL statement used for mappers.
 */

namespace SmoothPHP\Framework\Database\Statements;

class MySQLCustomStatement extends MySQLStatement {

	public function createResult() {
		return null;
	}

}