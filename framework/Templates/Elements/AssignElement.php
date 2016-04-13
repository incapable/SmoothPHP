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

namespace SmoothPHP\Framework\Templates\Elements;

class AssignElement {
    private $varName;
    private $value;
    
    public function __construct($varName, array $value) {
        $this->varName = $varName;
        $this->value = $value;
    }
}
