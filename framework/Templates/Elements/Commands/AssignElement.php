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

use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\Compiler\PHPBuilder;
use SmoothPHP\Framework\Templates\Compiler\TemplateLexer;
use SmoothPHP\Framework\Templates\Elements\Chain;
use SmoothPHP\Framework\Templates\Elements\Element;
use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;
use SmoothPHP\Framework\Templates\TemplateCompiler;

class AssignElement extends Element {
    private $varName;
    private $value;

    public static function handle(TemplateCompiler $compiler, TemplateLexer $command, TemplateLexer $lexer, Chain $chain, $stackEnd) {
        $command->skipWhitespace();
        $command->peek('$');
        $varName = $command->readAlphaNumeric();

        $command->skipWhitespace();
        $value = new Chain();
        $compiler->handleCommand($command, $lexer, $value, $stackEnd);
        $chain->addElement(new self($varName, TemplateCompiler::flatten($value)));

    }

    public function __construct($varName, Element $value) {
        $this->varName = $varName;
        $this->value = $value;
    }

    public function optimize(CompilerState $tpl) {
        $this->value = $this->value->optimize($tpl);

        if ($this->value instanceof PrimitiveElement)
            $tpl->vars[$this->varName] = $this->value;

        return $this;
    }

    public function writePHP(PHPBuilder $php) {
        $php->openPHP();
        $php->append(sprintf('$%s = %s;', $this->varName, $this->value->writePHP($php)));
    }
}
