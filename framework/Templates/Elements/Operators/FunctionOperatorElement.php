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

use SmoothPHP\Framework\Templates\Compiler\TemplateState;
use SmoothPHP\Framework\Templates\Elements\Chain;
use SmoothPHP\Framework\Templates\Elements\Element;
use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;

class FunctionOperatorElement extends Element {
    private $functionName;
    private $args;

    public function __construct($functionName, Chain $args) {
        $this->functionName = $functionName;
        $this->args = $args;
    }

    public function simplify(TemplateState $tpl) {
        $simpleArgs = true;
        $args = $this->args->getAll();
        $primitiveArgs = array();
        for ($i = 0; $i < count($args); $i++) {
            $args[$i] = $args[$i]->simplify($tpl);

            if (!($args[$i] instanceof PrimitiveElement))
                $simpleArgs = false;
            else
                $primitiveArgs[] = $args[$i]->getValue();
        }

        if ($simpleArgs) {
            return new PrimitiveElement(call_user_func_array($this->functionName, $primitiveArgs));
        } else
            return $this;
    }
}
