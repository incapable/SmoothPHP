<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * PlusOperatorElement.php
 * Handles adding 2 elements (+)
 */

namespace SmoothPHP\Framework\Templates\Elements\Operators;

use SmoothPHP\Framework\Templates\Elements\Element;

class PlusOperatorElement extends Element {
    private $left, $right;
    
    public function __construct($left, $right) {
        $this->left = $this->flatten($left);
        $this->right = $this->flatten($right);
    }
}