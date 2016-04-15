<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * ArithmeticOperatorElement.php
 * Arithmetic operation that attempts to order arithmetic operators properly
 */

namespace SmoothPHP\Framework\Templates\Elements\Operators;

use SmoothPHP\Framework\Templates\Elements\Element;

use SmoothPHP\Framework\Templates\TemplateCompiler;
use SmoothPHP\Framework\Templates\Elements\Chain;
use SmoothPHP\Framework\Templates\Compiler\TemplateLexer;

abstract class ArithmeticOperatorElement extends Element {
    protected $left, $right;
    
    protected abstract function getPriority();
    
    public static function handle(TemplateCompiler $compiler, TemplateLexer $command, TemplateLexer $lexer, Chain $chain) {
        $op;
        switch($command->next()) {
            case '+':
                $op = new PlusOperatorElement();
                break;
            case '*':
                $op = new MultiplicationOperatorElement();
                break;
        }
        $command->skipWhitespace();

        $right = new Chain();
        $compiler->handleCommand($command, $lexer, $right, ')');
        $chain->addElement(ArithmeticOperatorElement::determineOrder($chain->pop(), TemplateCompiler::flatten($right), $op));
    }
    
    public static function determineOrder(Element $previous, Element $next, ArithmeticOperatorElement $op) {
        if ($previous instanceof ArithmeticOperatorElement && $previous->getPriority() <= $op->getPriority()) {
            $left = $previous->left;
            $previous->left = $op;
            $op->left = $left;
            $op->right = $next;
            return $previous;
        } else if ($next instanceof ArithmeticOperatorElement && $next->getPriority() < $op->getPriority()) {
            $left = $next->left;
            $next->left = $op;
            $op->left = $previous;
            $op->right = $left;
            return $next;
        } else {
            $op->left = $previous;
            $op->right = $next;
            return $op;
        }
    }
    
}
