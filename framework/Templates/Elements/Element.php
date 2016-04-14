<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Element.php
 * 
 */

namespace SmoothPHP\Framework\Templates\Elements;

abstract class Element {
    
    protected function flatten($pieces) {
        if (is_array($pieces) && count($pieces) == 1)
            return $this->flatten(current($pieces));
        else
            return $pieces;
    }
    
}
