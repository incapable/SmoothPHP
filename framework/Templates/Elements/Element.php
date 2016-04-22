<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Element.php
 * An abstract template element.
 */

namespace SmoothPHP\Framework\Templates\Elements;

use SmoothPHP\Framework\Templates\Compiler\CompilerState;

abstract class Element {

    /**
     * @param CompilerState $tpl
     * @return Element
     */
    abstract function optimize(CompilerState $tpl);

    /**
     * @param CompilerState $tpl
     */
    abstract function output(CompilerState $tpl);

}
