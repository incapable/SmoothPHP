<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * VariableElement.php
 * Element that outputs the current value of a variable.
 */

namespace SmoothPHP\Framework\Templates\Elements\Commands;

use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\Compiler\PHPBuilder;
use SmoothPHP\Framework\Templates\Elements\Element;

class VariableElement extends Element {
    private $varName;

    public function __construct($varName) {
        $this->varName = $varName;
    }

    public function getVarName() {
        return $this->varName;
    }

    public function optimize(CompilerState $tpl) {
        if (isset($tpl->vars[$this->varName]))
            return $tpl->vars[$this->varName]->optimize($tpl);
        else
            return $this;
    }

    public function writePHP(PHPBuilder $php) {
        $php->openPHP();
        $php->append(sprintf('$_smooth_tpl->get_var(\'%s\')', $this->varName));
    }
}
