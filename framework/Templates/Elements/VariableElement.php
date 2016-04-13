<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * VariableElement.php
 * Element that outputs the current value of a variable.
 */

namespace SmoothPHP\Framework\Templates\Elements;

class VariableElement extends Element {
    private $varName;
    
    public function __construct($varName) {
        $this->varName = $varName;
    }
}
