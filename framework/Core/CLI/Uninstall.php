<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Uninstall.php
 * Wipes the database that is in-use.
 */

namespace SmoothPHP\Framework\Core\CLI;

use SmoothPHP\Framework\Core\Kernel;

class Uninstall extends Command {

    public function getDescription() {
        return 'Wipes the in-use database.';
    }

    public function handle(Kernel $kernel, array $argv) {
        printf( 'THIS METHOD WILL EMPTY DATABASE \'%s\', ARE YOU ABSOLUTELY SURE? [y/n]' . PHP_EOL, $kernel->getConfig()->mysql_database );
        $line = trim( fgets( STDIN ) );

        if ( $line != 'y' )
            throw new \RuntimeException( 'Cancelled.' . PHP_EOL );

        (new Cache())->handle($kernel, array());

        $mysql = $kernel->getMySQL();

        print( 'Dropping constraints...' . PHP_EOL );
        $constraints = $mysql->fetch( "
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
			    K.REFERENCED_TABLE_SCHEMA = %s", array( $kernel->getConfig()->mysql_database ) );

        if ( $constraints->hasData() ) {
            do {
                printf( 'Executing: %s' . PHP_EOL, $constraints->query );
                $mysql->execute( $constraints->query );
            } while ( $constraints->next() );
        } else {
            print( 'Nothing to do!' . PHP_EOL );
        }

        print( 'Dropping tables...' . PHP_EOL );
        $databases = $mysql->fetch( "
			SELECT
			    concat('DROP TABLE IF EXISTS `', table_name, '`;') AS query
			FROM
			    information_schema.tables
			WHERE
			    table_schema = %s", array( $kernel->getConfig()->mysql_database ) );

        if ( $databases->hasData() ) {
            do {
                printf( 'Executing: %s' . PHP_EOL, $databases->query );
                $mysql->execute( $databases->query );
            } while ( $databases->next() );
        } else {
            print( 'Nothing to do!' . PHP_EOL );
        }

        print( 'Done clearing database.' . PHP_EOL );
    }

}
