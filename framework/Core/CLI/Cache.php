<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Cache.php
 * Clears the cache.
 */

namespace SmoothPHP\Framework\Core\CLI;

use SmoothPHP\Framework\Core\Kernel;

class Cache extends Command {

	public function getDescription() {
		return 'Deletes the cache.';
	}

	public function handle(Kernel $kernel, array $argv) {
		$this->traverse(__ROOT__ . 'cache', function ($file, $isDir) {
			if ($isDir)
				rmdir($file);
			else
				unlink($file);
		});
		print('Cache has been cleared.' . PHP_EOL);
	}

}
