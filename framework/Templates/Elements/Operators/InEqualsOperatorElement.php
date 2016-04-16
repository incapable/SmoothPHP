<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * InEqualsOperatorElement.php
 * Inequals operator, returns a boolean value representing what the value does not evaluate to
 */

namespace SmoothPHP\Framework\Templates\Elements\Operators;

use SmoothPHP\Framework\Templates\Compiler\TemplateLexer;
use SmoothPHP\Framework\Templates\Compiler\TemplateState;
use SmoothPHP\Framework\Templates\Elements\Chain;
use SmoothPHP\Framework\Templates\Elements\Element;
use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;
use SmoothPHP\Framework\Templates\TemplateCompiler;

class InEqualsOperatorElement extends Element {
    private $left, $right;

    public static function handle(TemplateCompiler $compiler, TemplateLexer $command, TemplateLexer $lexer, Chain $chain, $stackEnd) {
        $command->next();
        if ($command->peek('=')) {
            $right = new Chain();
            $compiler->handleCommand($command, $lexer, $right, $stackEnd);
            $chain->addElement(new self($chain->pop(), TemplateCompiler::flatten($right)));
        }
    }

    public function __construct($left, $right) {
        $this->left = $left;
        $this->right = $right;
    }

    public function simplify(TemplateState $tpl) {
        $this->left = $this->left->simplify($tpl);
        $this->right = $this->right->simplify($tpl);

        if ($this->left instanceof PrimitiveElement && $this->right instanceof PrimitiveElement)
            return new PrimitiveElement($this->left->getValue() != $this->right->getValue());
        else
            return $this;
    }
}
