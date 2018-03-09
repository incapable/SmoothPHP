<?php

/**
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * **********
 * Copyright © 2015-2018
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * **********
 * Uninstall.php
 */

namespace SmoothPHP\Framework\Core\CLI;

use SmoothPHP\Framework\Core\Kernel;

class Uninstall extends Command {

	public function getDescription() {
		return 'Wipes the in-use database.';
	}

	public function handle(Kernel $kernel, array $argv) {
		if (file_exists(__ROOT__ . '/production.lock')) {
			printf('Can not run uninstall script because production.lock exists.' . PHP_EOL);
			return;
		}

		printf('THIS METHOD WILL EMPTY DATABASE \'%s\', ARE YOU ABSOLUTELY SURE? [y/n]' . PHP_EOL, $kernel->getConfig()->mysql_database);
		$line = trim(fgets(STDIN));

		if ($line != 'y')
			throw new \RuntimeException('Cancelled.' . PHP_EOL);

		(new Cache())->handle($kernel, []);

		$mysql = $kernel->getMySQL();

		print('Dropping constraints...' . PHP_EOL);
		$constraints = $mysql->fetch("
			SELECT DISTINCT
			    CONCAT('ALTER TABLE `',
			            K.TABLE_NAME,
			            '` DROP FOREIGN KEY `',
			            K.CONSTRAINT_NAME,
			            '`;') AS query
			FROM
			    information_schema.KEY_COLUMN_USAGE K
			        LEFT JOIN
			    information_schema.REFERENTIAL_CONSTRAINTS C USING (CONSTRAINT_NAME)
			WHERE
			    K.REFERENCED_TABLE_SCHEMA = %s", [$kernel->getConfig()->mysql_database]);

		if ($constraints->hasData()) {
			do {
				printf('Executing: %s' . PHP_EOL, $constraints->query);
				$mysql->execute($constraints->query);
			} while ($constraints->next());
		} else {
			print('Nothing to do!' . PHP_EOL);
		}

		print('Dropping tables...' . PHP_EOL);
		$databases = $mysql->fetch("
			SELECT
			    concat('DROP TABLE IF EXISTS `', table_name, '`;') AS query
			FROM
			    information_schema.tables
			WHERE
			    table_schema = %s", [$kernel->getConfig()->mysql_database]);

		if ($databases->hasData()) {
			do {
				printf('Executing: %s' . PHP_EOL, $databases->query);
				$mysql->execute($databases->query);
			} while ($databases->next());
		} else {
			print('Nothing to do!' . PHP_EOL);
		}

		print('Done clearing database.' . PHP_EOL);
	}

}
