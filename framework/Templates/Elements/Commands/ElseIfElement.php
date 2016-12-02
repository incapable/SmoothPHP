<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * ElseIfElement.php
 * Part of IfElement, will output the rest if its own condition is true.
 */

namespace SmoothPHP\Framework\Templates\Elements\Commands;

use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\Compiler\TemplateLexer;
use SmoothPHP\Framework\Templates\Elements\Chain;
use SmoothPHP\Framework\Templates\Elements\Element;
use SmoothPHP\Framework\Templates\TemplateCompiler;

class ElseIfElement extends Element {

    public static function handle(TemplateCompiler $compiler, TemplateLexer $command, TemplateLexer $lexer, Chain $chain, $stackEnd = null) {
        $chain->addElement(new self());
        IfElement::handle($compiler, $command, $lexer, $chain, $stackEnd);
    }

    public function optimize(CompilerState $tpl) {
        return $this;
    }

    public function output(CompilerState $tpl) {
    }
}