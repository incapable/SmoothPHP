<?php

/**
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * **********
 * Copyright © 2015-2018
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * **********
 * Install.php
 */

namespace SmoothPHP\Framework\Core\CLI;

use SmoothPHP\Framework\Core\Kernel;
use SmoothPHP\Framework\Database\Database;
use SmoothPHP\Framework\Database\DatabaseException;

class Install extends Command {

	public function getDescription() {
		return 'Inserts all SQL files into the database. Not for use during production.';
	}

	public function handle(Kernel $kernel, array $argv) {
		if (file_exists(__ROOT__ . '/production.lock')) {
			printf('Can not run install script because production.lock exists.' . PHP_EOL);
			return;
		}

		$debug = true;
		if (isset($argv[0]) && strtolower($argv[0]) == '--nodebug')
			$debug = false;

		$this->traverse(__ROOT__ . 'framework/meta/sql', function ($file) use ($kernel, $debug) {
			switch (pathinfo($file, PATHINFO_FILENAME)) {
				case 'authentication':
					if (!$kernel->getConfig()->authentication_enabled)
						break;

				// fallthrough
				default:
					$this->import($kernel->getDatabase(), $file, $debug);
			}
		});
		$this->traverse(__ROOT__ . 'src/sql', function ($file) use ($kernel, $debug) {
			$this->import($kernel->getDatabase(), $file, $debug);
		});
	}

	private function import(Database $db, $file, $debug) {
		if (!strpos($file, '.sql'))
			return; // Skip non-sql file

		if (!$debug && strpos($file, '.debug.sql')) {
			printf('Skipping %s...' . PHP_EOL, $file);
			return;
		}

		printf('Importing %s... ', $file);
		$sqlFile = file_get_contents($file);

		$queries = explode(';', $sqlFile);
		$count = 0;
		$insert_id = 0;

		$db->start();
		try {
			foreach ($queries as $query) {
				if (strlen(preg_replace('( |\n|\r|' . PHP_EOL . ')', '', $query)) == 0)
					continue;

				$query = str_replace('LAST_INSERT_ID()', $insert_id, $query);
				$insert_id = $db->execute($query);
				$count++;
			}

			$db->commit();
		} catch (\Exception $e) {
			$db->rollback();
			/** @noinspection PhpUnhandledExceptionInspection */
			throw $e;
		}

		printf('%d queries executed.' . PHP_EOL, $count);
	}

}