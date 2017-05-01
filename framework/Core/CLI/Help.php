<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Help.php
 * Lists all available CLI commands.
 */

namespace SmoothPHP\Framework\Core\CLI;

use SmoothPHP\Framework\Core\Kernel;

class Help extends Command {

	public function getDescription() {
		return 'Shows this list of commands.';
	}

	public function handle(Kernel $kernel, array $argv) {
		$dir = opendir(__DIR__);
		while (($file = readdir($dir)) !== false) {
			if (is_dir($file) || $file == 'Command.php')
				continue;

			$fileName = explode('.', $file)[0];
			$className = __NAMESPACE__ . '\\' . $fileName;
			/* @var $cmd Command */
			$cmd = new $className();
			printf('smoothphp %s - %s%s', strtolower($fileName), $cmd->getDescription(), PHP_EOL);
		}
	}

}