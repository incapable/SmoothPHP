<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Install.php
 * Inserts all SQL files into the database. Not for use during production.
 */

namespace SmoothPHP\Framework\Core\CLI;

use SmoothPHP\Framework\Core\Kernel;
use SmoothPHP\Framework\Database\MySQL;

class Install extends Command {

    public function getDescription() {
        return 'Inserts all SQL files into the database. Not for use during production.';
    }

    public function handle(Kernel $kernel, array $argv) {
        $this->traverse(__ROOT__ . 'framework/meta/sql', function($file) use ($kernel) {
            switch(pathinfo($file, PATHINFO_FILENAME)) {
                case 'authentication':
                    if (!$kernel->getConfig()->authentication_enabled)
                        break;
                default:
                    $this->import($kernel->getMySQL(), $file);
            }
        });
        $this->traverse(__ROOT__ . 'src/sql', function ($file) use ($kernel) {
            $this->import($kernel->getMySQL(), $file);
        });
    }

    private function import(MySQL $mysql, $file) {
        printf( 'Importing %s... ' . PHP_EOL, $file );
        $sqlFile = file_get_contents( $file );

        $queries = explode( ';', $sqlFile );
        $count = 0;
        $insert_id = 0;
        foreach ( $queries as $query ) {
            if ( strlen( preg_replace( '( |\n|\r|' . PHP_EOL . ')', '', $query ) ) == 0 )
                continue;

            $query = str_replace( 'LAST_INSERT_ID()', $insert_id, $query );
            $insert_id = $mysql->execute($query);
            $count++;
        }

        printf( '%d queries executed.' . PHP_EOL, $count );
    }

}