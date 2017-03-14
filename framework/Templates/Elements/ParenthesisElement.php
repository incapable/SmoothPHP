<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * ParenthesisElement.php
 * Used as a temporary language construct
 */

namespace SmoothPHP\Framework\Templates\Elements;

use SmoothPHP\Framework\Templates\Compiler\CompilerState;

class ParenthesisElement extends Element {
    private $element;

    public function __construct(Element $element) {
        $this->element = $element;
    }

    public function optimize(CompilerState $tpl) {
        return $this->element->optimize($tpl);
    }

    public function output(CompilerState $tpl) {
        $this->element->output($tpl);
    }
}
