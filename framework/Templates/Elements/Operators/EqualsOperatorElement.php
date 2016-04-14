<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * EqualsOperatorElement.php
 * Equals operator, returns a boolean value if the condition evaluates to true
 */

namespace SmoothPHP\Framework\Templates\Elements\Operators;

use SmoothPHP\Framework\Templates\Elements\Element;

class EqualsOperatorElement extends Element {
    private $left, $right;
    
    public function __construct($left, $right) {
        $this->left = $this->flatten($left);
        $this->right = $this->flatten($right);
    }
}
