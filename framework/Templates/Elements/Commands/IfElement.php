<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * IfCommand.php
 * Description
 */

namespace SmoothPHP\Framework\Templates\Elements\Commands;

use SmoothPHP\Framework\Templates\Elements\Element;

class IfElement extends Element{
    private $condition;
    private $body;
    
    public function __construct($condition, $body) {
        $this->condition = $condition;
        $this->body = $body;
    }
}
