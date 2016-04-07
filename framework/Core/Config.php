<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Config.php
 * Abstract configuration for SmoothPHP
 */

namespace SmoothPHP\Framework\Core;

class Config {
    public $debug = false;

    public $mysql_enabled = false;
    public $mysql_host = 'localhost';
    public $mysql_database = 'smoothphp';
    public $mysql_port = 3306;
    public $mysql_user = 'root';
    public $mysql_password = '';
}
