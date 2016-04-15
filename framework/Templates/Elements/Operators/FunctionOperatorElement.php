<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * FunctionOperatorElement.php
 * Element that calls a function.
 */

namespace SmoothPHP\Framework\Templates\Elements\Operators;

use SmoothPHP\Framework\Templates\Elements\Element;

class FunctionOperatorElement extends Element {
    private $functionName;
    private $args;
    
    public function __construct($functionName, array $args) {
        $this->functionName = $functionName;
        $this->args = $args;
    }
    
    public function asPHP() {
        $args = array();
        foreach($this->args as $arg)
            $args[] = $arg->asPHP();
        return sprintf('%s(%s)', $this->functionName->asPHP(), implode(',', $args));
    }
}
