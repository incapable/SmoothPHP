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

use SmoothPHP\Framework\Templates\TemplateState;
use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;

class PlusOperatorElement extends ArithmeticOperatorElement {
    
    public function getPriority() {
        return 2;
    }
    
    public function simplify(TemplateState $tpl) {
        $this->left = $this->left->simplify($tpl);
        $this->right = $this->right->simplify($tpl);
        
        if ($this->left instanceof PrimitiveElement && $this->right instanceof PrimitiveElement)
            if (is_string($this->left->getValue()) && is_string($this->right->getValue()))
                return new PrimitiveElement($this->left->getValue() . $this->right->getValue());
            else
                return new PrimitiveElement($this->left->getValue() + $this->right->getValue());
        else
            return $this;
    }
    
}
