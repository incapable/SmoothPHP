<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * IfCommand.php
 * Description
 */

namespace SmoothPHP\Framework\Templates\Elements\Commands;

use SmoothPHP\Framework\Templates\TemplateCompiler;
use SmoothPHP\Framework\Templates\Compiler\TemplateLexer;
use SmoothPHP\Framework\Templates\Elements\Chain;

use SmoothPHP\Framework\Templates\Compiler\TemplateState;
use SmoothPHP\Framework\Templates\Elements\Element;
use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;

class IfElement extends Element{
    private $condition;
    private $body;
    
    public static function handle(TemplateCompiler $compiler, TemplateLexer $command, TemplateLexer $lexer, Chain $chain, $stackEnd = null) {
        $condition = new Chain();
        $compiler->handleCommand($command, $lexer, $condition, $stackEnd);
        $body = new Chain();
        $compiler->read($lexer, $body, '{/if}');
        $chain->addElement(new self(TemplateCompiler::flatten($condition), TemplateCompiler::flatten($body)));
    }
    
    public function __construct($condition, Element $body) {
        $this->condition = $condition;
        $this->body = $body;
    }
    
    public function simplify(TemplateState $tpl) {
        $this->condition = $this->condition->simplify($tpl);
        $this->body = $this->body->simplify($tpl);
        
        if ($this->condition instanceof PrimitiveElement) {
            if ($this->condition->getValue())
                return $this->body;
            else
                return new PrimitiveElement('');
        } else
            return $this;
    }
}
