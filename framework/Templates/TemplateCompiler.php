<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * TemplateCompiler.php
 * Template compiler, builds the first level of cache.
 */

namespace SmoothPHP\Framework\Templates;

use SmoothPHP\Framework\Templates\Elements\Chain;
use SmoothPHP\Framework\Templates\Elements\Commands\BlockElement;
use SmoothPHP\Framework\Templates\Elements\Operators\ArithmeticOperatorElement;

class TemplateCompiler {
    const DELIMITER_START = '{';
    const DELIMITER_END = '}';
    
    private $operators;
    
    public function __construct() {
        $this->operators = array(
            '+' => Elements\Operators\PlusOperatorElement::class,
            '*' => Elements\Operators\MultiplicationOperatorElement::class
        );
    }
    
    public function compile($file) {
        $lexer = new TemplateLexer(file_get_contents($file));
        
        $chain = new Chain();
        $this->read($lexer, $chain);
        
        $tpl = new TemplateState();
        $chain = $chain->simplify($tpl);
        
        return $chain;
    }
    
    private function read(TemplateLexer $lexer, Chain $chain, $stackEnd = null) {
        $rawString = '';
        
        $finishString = function() use (&$chain, &$rawString) {
            if (strlen(trim($rawString)) > 0)
                $chain->addElement (new Elements\PrimitiveElement($rawString));
            $rawString = '';
        };
        
        while(true) {
            if ( $stackEnd !== null && $lexer->peek($stackEnd) ) {
                $finishString();
                return;
            } else if ( $lexer->peek(self::DELIMITER_START) ) {
                if ($lexer->isWhitespace()) {
                    $rawString .= self::DELIMITER_START;
                    continue;
                }
                
                $command = '';
                while(!$lexer->peek(self::DELIMITER_END)) {
                    $char = $lexer->next();
                    if ($char !== false)
                        $command .= $char;
                    else
                        throw new TemplateCompileException("Template syntax error, unfinished command: " . self::DELIMITER_START . $command);
                }
                
                $finishString();
                
                $this->handleCommand(new TemplateLexer($command), $lexer, $chain);
            } else {
                $char = $lexer->next();
                if ($char !== false)
                    $rawString .= $char;
                else {
                    $finishString();
                    return;
                }
            }
        }
    }
    
    private function readRaw(TemplateLexer $lexer, $stackEnd = null, $stackEscape = null) {
        $rawString = '';
        
        while(true) {
            if ($stackEscape != null && $lexer->peek($stackEscape)) {
                $rawString .= $stackEnd;
            } else if ($stackEnd != null && $lexer->peek($stackEnd)) {
                return $rawString;
            } else {
                $char = $lexer->next();
                if ($char !== false)
                    $rawString .= $char;
                else
                    return $rawString;
            }
        }
    }
    
    private function readAlphaNumeric(TemplateLexer $lexer) {
        $rawString = '';
        
        while(true) {
            if (ctype_alnum($lexer->peekSingle()))
                $rawString .= $lexer->next();
            else
                return $rawString;
        }
    }
   
    private function handleCommand(TemplateLexer $command, TemplateLexer $lexer, Chain $chain, $stackEnd = null) {
        $command->skipWhitespace();

        if ($stackEnd != null && $command->peek($stackEnd)) {
            return;
        } else if ($command->peek('(')) {
            $elements = new Chain();
            $this->handleCommand($command, $lexer, $elements, ')');
            $chain->addElement(new Elements\ParenthesisElement(self::flatten($elements)));
        } else if ($command->peek('$')) {
            $chain->addElement(new Elements\Commands\VariableElement($this->readAlphaNumeric($command)));
        } else {
            $next = $this->readAlphaNumeric($command);

            switch(strtolower($next)) {
                case 'assign': {
                    $command->skipWhitespace();
                    $command->peek('$');
                    $varName = $this->readAlphaNumeric($command);

                    $command->skipWhitespace();
                    $value = new Chain();
                    $this->handleCommand($command, $lexer, $value, $stackEnd);
                    $chain->addElement(new Elements\Commands\AssignElement($varName, self::flatten($value)));
                    break;
                }
                case 'if': {
                    $condition = new Chain();
                    $this->handleCommand($command, $lexer, $condition, $stackEnd);
                    $body = new Chain();
                    $this->read($lexer, $body, '{/if}');
                    $chain->addElement(new Elements\Commands\IfElement(self::flatten($condition), self::flatten($body)));
                    break;
                }
                case 'block': {
                    $args = new Chain();
                    $this->handleCommand($command, $lexer, $args);
                    $args = $args->getAll();
                    
                    $usage = BlockElement::USAGE_UNSPECIFIED;
                    if (isset($args[1])) {
                        $args[1] = $args[1]->simplify(new TemplateState());
                        switch($args[1]->getValue()) {
                            case 'prepend':
                                $usage = BlockElement::USAGE_PREPEND;
                                break;
                            case 'append':
                                $usage = BlockElement::USAGE_APPEND;
                        }
                    }
                    
                    $body = new Chain();
                    $this->read($lexer, $body, '{/block}');
                    $chain->addElement(new BlockElement($args[0], $usage, self::flatten($body)));
                    break;
                }
                default: {
                    if (strlen(trim($next)) > 0) {
                        $chain->addElement(new Elements\PrimitiveElement($next, true));
                        if ($command->peek('(')) {
                            // Function call
                            $name = $chain->pop();
                            if (!($name instanceof Elements\PrimitiveElement))
                                throw new TemplateCompileException("Attempting to call a function without a name.");
                            $args = new Chain();
                            do {
                                $this->handleCommand($command, $lexer, $args, ')');
                            } while($command->skipWhitespace() || $command->peek(','));
                            $chain->addElement(new Elements\Operators\FunctionOperatorElement($name->getValue(), $args));
                        }
                    } else {
                        $command->skipWhitespace();
                        switch($command->peekSingle()) {
                            case '\'':
                            case '"':
                                $strStart = $command->next();
                                $str = $this->readRaw($command, $strStart, '\\' . $strStart);
                                $chain->addElement(new Elements\PrimitiveElement($str));
                                break;
                            
                            case '+':
                            case '*':
                                $op = $command->next();
                                $command->skipWhitespace();

                                $right = new Chain();
                                $this->handleCommand($command, $lexer, $right, ')');
                                $chain->addElement(ArithmeticOperatorElement::determineOrder($chain->pop(), self::flatten($right), new $this->operators[$op]));
                                break;
                                
                            case '=':
                                $command->next();
                                if ($command->peek('=')) {
                                    $right = new Chain();
                                    $this->handleCommand($command, $lexer, $right, $stackEnd);
                                    $chain->addElement(new Elements\Operators\EqualsOperatorElement($chain->pop(), self::flatten($right)));
                                } else {
                                    $assignTo = $chain->pop();
                                    if (!($assignTo instanceof Elements\Commands\VariableElement))
                                        throw new TemplateCompileException("Attempting to assign a value to a non-variable.");
                                    
                                    $right = new Chain();
                                    $this->handleCommand($command, $lexer, $right, $stackEnd);
                                    $chain->addElement(new Elements\Commands\AssignElement($assignTo->getVarName(), self::flatten($right)));
                                }
                                break;
                            case '!':
                                $command->next();
                                if ($command->peek('=')) {
                                    $right = new Chain();
                                    $this->handleCommand($command, $lexer, $right, $stackEnd);
                                    $chain->addElement(new Elements\Operators\InEqualsOperatorElement($chain->pop(), self::flatten($right)));
                                }
                                break;
                            default:
                                if (strlen($command->peekSingle()) > 0)
                                    throw new TemplateCompileException("Unknown operator '" . $command->peekSingle() . "'");
                        }
                    }
                }
            }
        }
        
        $command->skipWhitespace();
        if ($command->peekSingle() && $command->peekSingle() != ',') // Do we have more to read?
            $this->handleCommand($command, $lexer, $chain, $stackEnd);
        return;
    }
    
    private static function flatten($chain) {
        if ($chain instanceof Chain) {
            $all = $chain->getAll();
            if (count($all) == 1)
                return self::flatten(current($all));
        }
        
        return $chain;
    }
    
}

class TemplateCompileException extends \Exception {
}