<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * TemplateRuntime.php
 * Description
 */

namespace SmoothPHP\Framework\Templates;

class TemplateRuntime {
    public $args;

    public function __construct() {
        $this->args = array();
    }
}