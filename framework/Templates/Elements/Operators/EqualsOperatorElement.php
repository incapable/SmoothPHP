<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * EqualsOperatorElement.php
 * Equals operator, returns a boolean value representing what the value evaluates to
 */

namespace SmoothPHP\Framework\Templates\Elements\Operators;

use SmoothPHP\Framework\Templates\TemplateState;
use SmoothPHP\Framework\Templates\Elements\Element;
use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;

class EqualsOperatorElement extends Element {
    private $left, $right;
    
    public function __construct($left, $right) {
        $this->left = $left;
        $this->right = $right;
    }
    
    public function simplify(TemplateState $tpl) {
        $this->left = $this->left->simplify($tpl);
        $this->right = $this->right->simplify($tpl);
        
        if ($this->left instanceof PrimitiveElement && $this->right instanceof PrimitiveElement)
            return new PrimitiveElement($this->left->getValue() == $this->right->getValue());
        else
            return $this;
    }
}
