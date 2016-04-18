<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * MySQL.php
 * Main entry point for all MySQL connections
 */

namespace SmoothPHP\Framework\Database;

use SmoothPHP\Framework\Core\Config;

class MySQL {
    private $connection;

    public function __construct(Config $config) {
        $prefix = ini_get('mysqli.allow_persistent') ? 'p:' : '';
        $this->connection = new \mysqli($prefix . $config->mysql_host, $config->mysql_user, $config->mysql_password, $config->mysql_database);
    }

    public function prepare($query) {
        $pquery = $this->connection->prepare($query);
    }

}
