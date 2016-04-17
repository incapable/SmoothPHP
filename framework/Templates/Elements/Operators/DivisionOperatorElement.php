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

use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\Compiler\PHPBuilder;
use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;

class DivisionOperatorElement extends ArithmeticOperatorElement {

    public function getPriority() {
        return 1;
    }

    public function optimize(CompilerState $tpl) {
        $this->left = $this->left->optimize($tpl);
        $this->right = $this->right->optimize($tpl);

        if ($this->left instanceof PrimitiveElement && $this->right instanceof PrimitiveElement)
            return new PrimitiveElement($this->left->getValue() / $this->right->getValue());
        else
            return $this;
    }

    public function writePHP(PHPBuilder $php) {
        $php->openPHP();
        $php->append(sprintf('(%s / %s)', $this->left->writePHP($php), $this->right->writePHP($php)));
    }
}
