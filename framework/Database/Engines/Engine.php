<?php
/**
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * **********
 * Copyright © 2015-2019
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * **********
 * Engine.php
 */

namespace SmoothPHP\Framework\Database\Engines;

use SmoothPHP\Framework\Core\Config;

interface Engine {
	public function connect(Config $config);

	public function disconnect();

	public function start();

	public function commit();

	public function rollback();

	public function prepare($query, array &$params = []);
}

interface Statement {
	public function execute();

	public function getInsertID();

	public function getResults();
}