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

use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;

class FunctionOperatorElement extends Element {
    private $functionName;
    private $args;
    
    public function __construct(Element $functionName, array $args) {
        $this->functionName = $functionName;
        $this->args = $args;
    }
    
    public function simplify(array &$vars) {
        $this->functionName = $this->functionName->simplify($vars);

        $simpleArgs = true;
        $primitiveArgs = array();
        for($i = 0; $i < count($this->args); $i++) {
            $this->args[$i] = $this->args[$i]->simplify($vars);
            
            if (!($this->args[$i] instanceof PrimitiveElement))
                $simpleArgs = false;
            else
                $primitiveArgs[] = $this->args[$i]->getValue();
        }

        if ($this->functionName instanceof PrimitiveElement && $simpleArgs) {
            return new PrimitiveElement(call_user_func_array($this->functionName->getValue(), $primitiveArgs));
        } else
            return $this;
    }
}
