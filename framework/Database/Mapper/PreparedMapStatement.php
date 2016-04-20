<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * PreparedMapStatement.php
 * Description
 */

namespace SmoothPHP\Framework\Database\Mapper;

class PreparedMapStatement {
    public $params, $references;
    /* @var $statement \SmoothPHP\Framework\Database\MySQLStatement */
    public $statement;

    public function __construct() {
        $this->params = array();
        $this->references = array();
    }
}