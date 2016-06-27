<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * AdditionOperatorElement.php
 * Handles adding 2 elements (+)
 */

namespace SmoothPHP\Framework\Templates\Elements\Operators;

use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;

class AdditionOperatorElement extends ArithmeticOperatorElement {

    public function getPriority() {
        return 2;
    }

    public function optimize(CompilerState $tpl) {
        $this->left = $this->left->optimize($tpl);
        $this->right = $this->right->optimize($tpl);

        if ($this->left instanceof PrimitiveElement && $this->right instanceof PrimitiveElement)
            if (is_string($this->left->getValue()) && is_string($this->right->getValue()))
                return new PrimitiveElement($this->left->getValue() . $this->right->getValue());
            else
                return new PrimitiveElement($this->left->getValue() + $this->right->getValue());
        else
            return $this;
    }
}