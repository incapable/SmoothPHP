<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * AssignElement.php
 * Element that will assign a variable to the currently active scope
 */

namespace SmoothPHP\Framework\Templates\Elements\Commands;

use SmoothPHP\Framework\Templates\Elements\Element;
use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;

class AssignElement extends Element {
    private $varName;
    private $value;
    
    public function __construct($varName, Element $value) {
        $this->varName = $varName;
        $this->value = $value;
    }
    
    public function simplify(array &$vars) {
        $this->value = $this->value->simplify($vars);
        
        if ($this->value instanceof PrimitiveElement)
            $vars[$this->varName] = $this->value;
        
        return $this;
    }
}
