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
        $left = $this->left->optimize($tpl);
        $right = $this->right->optimize($tpl);

        if ($left instanceof PrimitiveElement && $right instanceof PrimitiveElement)
            return new PrimitiveElement($left->getValue() == $right->getValue());
        else
            return new self($left, $right);
    }

    public function output(CompilerState $tpl) {
        $result = $this->optimize($tpl);

        if (!($result instanceof PrimitiveElement))
            throw new TemplateCompileException("Could not determine values at runtime.");

        $result->output($tpl);
    }
}
