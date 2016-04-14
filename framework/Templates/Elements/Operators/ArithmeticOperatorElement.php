<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * ArithmeticOperatorElement.php
 * Arithmetic operation that attempts to order arithmetic operators properly
 */

namespace SmoothPHP\Framework\Templates\Elements\Operators;

use SmoothPHP\Framework\Templates\Elements\Element;

abstract class ArithmeticOperatorElement extends Element {
    private $left, $right;
    
    public function __construct($left, $right) {
        $this->left = $left;
        $this->right = $right;
    }
    
    protected abstract function getPriority();
    
    public static function determineOrder(ArithmeticOperatorElement $high, Element $low) {
        if ($low instanceof ArithmeticOperatorElement && $high->getPriority() > $low->getPriority()) {
            $right = $low->right;
            $low->right = $high;
            $high->right = $right;
            return $low;
        } else {
            return $high;
        }
    }
}
