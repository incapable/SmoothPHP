<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * ParenthesisElement.php
 * Used as a temporary language construct
 */

namespace SmoothPHP\Framework\Templates\Elements;

use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\Compiler\PHPBuilder;
use SmoothPHP\Framework\Templates\Compiler\TemplateCompileException;

class ParenthesisElement extends Element {
    private $element;

    public function __construct(Element $element) {
        $this->element = $element;
    }

    public function optimize(CompilerState $tpl) {
        $this->element = $this->element->optimize($tpl);
        return $this->element;
    }

    public function writePHP(PHPBuilder $php) {
        throw new TemplateCompileException("Parenthesis being written.");
    }
}
