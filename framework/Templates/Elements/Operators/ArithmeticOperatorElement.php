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
    protected $left, $right;
    
    protected abstract function getPriority();
    
    public static function determineOrder(Element $previous, Element $next, ArithmeticOperatorElement $op) {
        if ($previous instanceof ArithmeticOperatorElement && $previous->getPriority() <= $op->getPriority()) {
            $left = $previous->left;
            $previous->left = $op;
            $op->left = $left;
            $op->right = $next;
            return $previous;
        } else if ($next instanceof ArithmeticOperatorElement && $next->getPriority() < $op->getPriority()) {
            $left = $next->left;
            $next->left = $op;
            $op->left = $previous;
            $op->right = $left;
            return $next;
        } else {
            $op->left = $previous;
            $op->right = $next;
            return $op;
        }
    }
}
