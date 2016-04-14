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

class MultiplicationOperatorElement extends ArithmeticOperatorElement {
    
    public function getPriority() {
        return 4;
    }
    
}
