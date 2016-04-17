<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Chain.php
 * Represents several elements as 1 total 'body'.
 */

namespace SmoothPHP\Framework\Templates\Elements;

use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\Compiler\PHPBuilder;

class Chain extends Element {
    private $chain;

    public function __construct() {
        $this->chain = array();
    }

    public function addElement(Element $element) {
        $this->chain[] = $element;
    }

    public function pop() {
        return array_pop($this->chain);
    }

    public function getAll() {
        return $this->chain;
    }

    public function optimize(CompilerState $tpl) {
        $chain = array();
        $str = '';

        foreach ($this->chain as $piece) {
            $piece = $piece->optimize($tpl);
            if ($piece instanceof PrimitiveElement)
                $str .= $piece->getValue();
            else {
                if (strlen(trim($str)) > 0) {
                    $chain[] = new PrimitiveElement($str);
                    $str = '';
                }
                $chain[] = $piece;
            }
        }

        if (strlen(trim($str)) > 0)
            $chain[] = new PrimitiveElement($str);

        $count = count($chain);
        if ($count == 0)
            return new PrimitiveElement();
        else if ($count == 1)
            return current($chain);
        else {
            $this->chain = $chain;
            return $this;
        }
    }

    public function writePHP(PHPBuilder $php) {
        array_map(function (Element $piece) use ($php) {
            $piece->writePHPInChain($php, true);
        }, $this->chain);
    }
}
