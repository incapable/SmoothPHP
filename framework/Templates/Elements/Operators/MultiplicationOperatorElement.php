<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * MultiplicationOperatorElement.php
 * Handles multiplying 2 elements (*)
 */

namespace SmoothPHP\Framework\Templates\Elements\Operators;

use SmoothPHP\Framework\Templates\Compiler\TemplateState;
use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;

class MultiplicationOperatorElement extends ArithmeticOperatorElement {

    public function getPriority() {
        return 4;
    }

    public function simplify(TemplateState $tpl) {
        $this->left = $this->left->simplify($tpl);
        $this->right = $this->right->simplify($tpl);

        if ($this->left instanceof PrimitiveElement && $this->right instanceof PrimitiveElement)
            return new PrimitiveElement($this->left->getValue() * $this->right->getValue());
        else
            return $this;
    }

}
