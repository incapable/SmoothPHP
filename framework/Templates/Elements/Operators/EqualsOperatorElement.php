<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * EqualsOperatorElement.php
 * Equals operator, returns a boolean value representing what the value evaluates to
 */

namespace SmoothPHP\Framework\Templates\Elements\Operators;

use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\Compiler\PHPBuilder;
use SmoothPHP\Framework\Templates\Compiler\TemplateCompileException;
use SmoothPHP\Framework\Templates\Compiler\TemplateLexer;
use SmoothPHP\Framework\Templates\Elements\Chain;
use SmoothPHP\Framework\Templates\Elements\Commands\AssignElement;
use SmoothPHP\Framework\Templates\Elements\Commands\VariableElement;
use SmoothPHP\Framework\Templates\Elements\Element;
use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;
use SmoothPHP\Framework\Templates\TemplateCompiler;

class EqualsOperatorElement extends Element {
    private $left, $right;

    public static function handle(TemplateCompiler $compiler, TemplateLexer $command, TemplateLexer $lexer, Chain $chain, $stackEnd) {
        $command->next();
        if ($command->peek('=')) {
            $right = new Chain();
            $compiler->handleCommand($command, $lexer, $right, $stackEnd);
            $chain->addElement(new self($chain->pop(), TemplateCompiler::flatten($right)));
        } else {
            $assignTo = $chain->pop();
            if (!($assignTo instanceof VariableElement))
                throw new TemplateCompileException("Attempting to assign a value to a non-variable.");

            $right = new Chain();
            $compiler->handleCommand($command, $lexer, $right, $stackEnd);
            $chain->addElement(new AssignElement($assignTo->getVarName(), TemplateCompiler::flatten($right)));
        }
    }

    public function __construct(Element $left, Element $right) {
        $this->left = $left;
        $this->right = $right;
    }

    public function optimize(CompilerState $tpl) {
        $this->left = $this->left->optimize($tpl);
        $this->right = $this->right->optimize($tpl);

        if ($this->left instanceof PrimitiveElement && $this->right instanceof PrimitiveElement)
            return new PrimitiveElement($this->left->getValue() == $this->right->getValue());
        else
            return $this;
    }

    public function writePHP(PHPBuilder $php) {
        $php->openPHP();
        $php->append('(');
        $this->left->writePHP($php);
        $php->append(' == ');
        $this->right->writePHP($php);
        $php->append(')');
    }
}
