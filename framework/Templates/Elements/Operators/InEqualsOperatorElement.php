<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * InEqualsOperatorElement.php
 * Inequals operator, returns a boolean value representing what the value does not evaluate to
 */

namespace SmoothPHP\Framework\Templates\Elements\Operators;

use SmoothPHP\Framework\Templates\Elements\Element;

class InEqualsOperatorElement extends Element {
    private $left, $right;
    
    public function __construct($left, $right) {
        $this->left = $left;
        $this->right = $right;
    }
}
