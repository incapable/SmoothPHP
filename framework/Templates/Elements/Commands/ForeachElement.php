<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * ForeachElement.php
 * Template loop, will iterate through an (php) array of elements.
 */

namespace SmoothPHP\Framework\Templates\Elements\Commands;

use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\Compiler\TemplateCompileException;
use SmoothPHP\Framework\Templates\Compiler\TemplateLexer;
use SmoothPHP\Framework\Templates\Elements\Chain;
use SmoothPHP\Framework\Templates\Elements\Element;
use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;
use SmoothPHP\Framework\Templates\TemplateCompiler;

class ForeachElement extends Element {
    private $iterable;
    private $keyName;
    private $valueName;
    private $body;

    public static function handle(TemplateCompiler $compiler, TemplateLexer $command, TemplateLexer $lexer, Chain $chain, $stackEnd = null) {
        $iterable = new Chain();
        $compiler->handleCommand($command, $lexer, $iterable, 'as');
        $command->skipWhitespace();
        $command->peek('$');
        $keyName = null;
        $valueName = $command->readAlphaNumeric();
        $command->skipWhitespace();
        if ($command->peekSingle() !== false) {
            $keyName = $valueName;
            $command->peek('$');
            $valueName = $command->readAlphaNumeric();
        }

        $body = new Chain();
        $compiler->read($lexer, $body, TemplateCompiler::DELIMITER_START . '/foreach' . TemplateCompiler::DELIMITER_END);
        $chain->addElement(new self(TemplateCompiler::flatten($iterable), $keyName, $valueName, TemplateCompiler::flatten($body)));
    }

    public function __construct(Element $iterable, $keyName, $valueName, Element $body) {
        $this->iterable = $iterable;
        $this->keyName = $keyName;
        $this->valueName = $valueName;
        $this->body = $body;
    }

    public function optimize(CompilerState $tpl) {
        $this->iterable = $this->iterable->optimize($tpl);
        $this->body = $this->body->optimize($tpl);

        if ($this->iterable instanceof PrimitiveElement)
            return $this->runLoop($tpl);

        return $this;
    }

    public function output(CompilerState $tpl) {
        $this->iterable = $this->iterable->optimize($tpl);
        $this->body = $this->body->optimize($tpl);

        if (!($this->iterable instanceof PrimitiveElement))
            throw new TemplateCompileException("Could not foreach-loop over element.");

        $this->runLoop($tpl)->output($tpl);
    }

    private function runLoop(CompilerState $tpl) {
        $result = new Chain();

        foreach($this->iterable->getValue() as $key => $value) {
            $scope = $tpl->createSubScope();
            if ($this->keyName != null)
                $scope->vars[$this->keyName] = new PrimitiveElement($key);
            $scope->vars[$this->valueName] = new PrimitiveElement($value);

            $clone = clone $this->body;
            $result->addElement($clone->optimize($scope));
        }

        return $result->optimize($tpl);
    }
}