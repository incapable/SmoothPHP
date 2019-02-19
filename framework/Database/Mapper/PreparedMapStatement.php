<?php

/**
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * **********
 * Copyright © 2015-2019
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * **********
 * PreparedMapStatement.php
 */

namespace SmoothPHP\Framework\Database\Mapper;

class PreparedMapStatement {
	public $params, $references;
	/* @var $statement \SmoothPHP\Framework\Database\Statements\SQLStatement */
	public $statement;

	public function __construct() {
		$this->params = [];
		$this->references = [];
	}
}