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

use SmoothPHP\Framework\Templates\Compiler\TemplateState;
use SmoothPHP\Framework\Templates\Elements\Element;

class VariableElement extends Element {
    private $varName;
    
    public function __construct($varName) {
        $this->varName = $varName;
    }
    
    public function getVarName() {
        return $this->varName;
    }
    
    public function simplify(TemplateState $tpl) {
        if (isset($tpl->vars[$this->varName]))
            return $tpl->vars[$this->varName]->simplify($tpl);
        else
            return $this;
    }
}
