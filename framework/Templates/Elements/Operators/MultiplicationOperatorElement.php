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

use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;
use SmoothPHP\Framework\Templates\Compiler\PHPBuilder;

class MultiplicationOperatorElement extends ArithmeticOperatorElement {

    public function getPriority() {
        return 4;
    }

    public function optimize(CompilerState $tpl) {
        $this->left = $this->left->optimize($tpl);
        $this->right = $this->right->optimize($tpl);

        if ($this->left instanceof PrimitiveElement && $this->right instanceof PrimitiveElement)
            return new PrimitiveElement($this->left->getValue() * $this->right->getValue());
        else
            return $this;
    }

    public function writePHP(PHPBuilder $php) {
        $php->openPHP();
        $php->append(sprintf('(%s * %s)', $this->left->writePHP($php), $this->right->writePHP($php)));
    }
}
