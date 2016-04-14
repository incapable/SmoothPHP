<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * ParenthesisElement.php
 * A list of output elements that are executed before their result is used.
 */

namespace SmoothPHP\Framework\Templates\Elements;

class ParenthesisElement extends Element {
    private $elements;
    
    public function __construct($elements) {
        $this->elements = $elements;
    }
}
