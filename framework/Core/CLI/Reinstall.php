<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Reinstall.php
 * Executes both uninstall and install.
 */

namespace SmoothPHP\Framework\Core\CLI;

use SmoothPHP\Framework\Core\Kernel;

class Reinstall extends Command {

	public function getDescription() {
		return 'Executes both uninstall and install.';
	}

	public function handle(Kernel $kernel, array $argv) {
		(new Uninstall())->handle($kernel, $argv);
		(new Install())->handle($kernel, $argv);
	}

}