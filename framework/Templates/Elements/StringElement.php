<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * StringElement.php
 * Template element of a 'raw' string.
 */

namespace SmoothPHP\Framework\Templates\Elements;

class StringElement extends Element {
    private $string;
    
    public function __construct($string) {
        $this->string = $string;
    }
    
    public function getValue() {
        return $this->string;
    }
}
