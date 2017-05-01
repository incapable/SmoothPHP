<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Hash.php
 * Convenience method for server admins to hash a password for manual user insertion.
 */

namespace SmoothPHP\Framework\Core\CLI;

use SmoothPHP\Framework\Core\Kernel;

class Hash extends Command {

	public function getDescription() {
		return 'Convenience command. Hashes a password.';
	}

	public function handle(Kernel $kernel, array $argv) {
		print('Enter a password to hash.' . PHP_EOL);

		if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')
			system('stty -echo');
		$line = trim(fgets(STDIN));
		if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')
			system('stty echo');
		print(PHP_EOL);

		$hash = password_hash($line, PASSWORD_BCRYPT);

		print($hash . PHP_EOL);
	}

}