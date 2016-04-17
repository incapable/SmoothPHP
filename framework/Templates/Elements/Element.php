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

use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\Compiler\PHPBuilder;

abstract class Element {

    abstract function optimize(CompilerState $tpl);

    public function writePHP(PHPBuilder $php) {
        $this->writePHPInChain($php);
    }

    public function writePHPInChain(PHPBuilder $php, $isChainPiece = false) {
        $this->writePHP($php);
    }

}
