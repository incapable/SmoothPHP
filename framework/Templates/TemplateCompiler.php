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
        $output = array();
        $this->read($lexer, $output);
        $vars = array();
        for($i = 0; $i < count($output); $i++)
            $output[$i] = $output[$i]->simplify($vars);
        return $output;
    }
    
    private function read(TemplateLexer $lexer, array &$output, $stackEnd = null) {
        $rawString = '';
        
        $finishString = function() use (&$output, &$rawString) {
            if (strlen(trim($rawString)) > 0)
                $output[] = new Elements\PrimitiveElement($rawString);
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
                
                $this->handleCommand(new TemplateLexer($command), $lexer, $output);
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
   
    private function handleCommand(TemplateLexer $command, TemplateLexer $lexer, array &$output, $stackEnd = null) {
        $command->skipWhitespace();

        if ($stackEnd != null && $command->peek($stackEnd)) {
            return;
        } else if ($command->peek('(')) {
            $elements = array();
            $this->handleCommand($command, $lexer, $elements, ')');
            $output[] = new Elements\ParenthesisElement(self::flatten($elements));
        } else if ($command->peek('$')) {
            $output[] = new Elements\Commands\VariableElement($this->readAlphaNumeric($command));
        } else {
            $next = $this->readAlphaNumeric($command);

            switch(strtolower($next)) {
                case 'assign': {
                    $command->skipWhitespace();
                    $command->peek('$');
                    $varName = $this->readAlphaNumeric($command);

                    $command->skipWhitespace();
                    $value = array();
                    $this->handleCommand($command, $lexer, $value, $stackEnd);
                    $output[] = new Elements\Commands\AssignElement($varName, self::flatten($value));
                    break;
                }
                case 'if': {
                    $condition = array();
                    $this->handleCommand($command, $lexer, $condition, $stackEnd);
                    $body = array();
                    $this->read($lexer, $body, '{/if}');
                    $output[] = new Elements\Commands\IfElement(self::flatten($condition), self::flatten($body));
                    break;
                }
                default: {
                    if (strlen(trim($next)) > 0) {
                        $output[] = new Elements\PrimitiveElement($next, true);
                        if ($command->peek('(')) {
                            // Function call
                            $name = array_pop($output);
                            if (!($name instanceof Elements\PrimitiveElement))
                                throw new TemplateCompileException("Attempting to call a function without a name.");
                            $args = array();
                            do {
                                $this->handleCommand($command, $lexer, $args, ')');
                            } while($command->skipWhitespace() || $command->peek(','));
                            $output[] = new Elements\Operators\FunctionOperatorElement($name, $args);
                        }
                    } else {
                        $command->skipWhitespace();
                        switch($command->peekSingle()) {
                            case '\'':
                            case '"':
                                $strStart = $command->next();
                                $str = $this->readRaw($command, $strStart, '\\' . $strStart);
                                $output[] = new Elements\PrimitiveElement($str);
                                break;
                            
                            case '+':
                            case '*':
                                $op = $command->next();
                                $command->skipWhitespace();

                                $right = array();
                                $this->handleCommand($command, $lexer, $right, ')');
                                $output[] = ArithmeticOperatorElement::determineOrder(array_pop($output), self::flatten($right), new $this->operators[$op]);
                                break;
                                
                            case '=':
                                $command->next();
                                if ($command->peek('=')) {
                                    $right = array();
                                    $this->handleCommand($command, $lexer, $right, $stackEnd);
                                    $output[] = new Elements\Operators\EqualsOperatorElement(array_pop($output), self::flatten($right));
                                } else {
                                    $assignTo = array_pop($output);
                                    if (!($assignTo instanceof Elements\Commands\VariableElement))
                                        throw new TemplateCompileException("Attempting to assign a value to a non-variable.");
                                    
                                    $right = array();
                                    $this->handleCommand($command, $lexer, $right, $stackEnd);
                                    $output[] = new Elements\Commands\AssignElement($assignTo->getVarName(), self::flatten($right));
                                }
                                break;
                            case '!':
                                $command->next();
                                if ($command->peek('=')) {
                                    $right = array();
                                    $this->handleCommand($command, $lexer, $right, $stackEnd);
                                    $output[] = new Elements\Operators\InEqualsOperatorElement(array_pop($output), self::flatten($right));
                                }
                                break;
                            default:
                                var_dump($stackEnd);
                                var_dump($command->remainder());
                                if (strlen($command->peekSingle()) > 0)
                                    throw new TemplateCompileException("Unknown operator '" . $command->peekSingle() . "'");
                        }
                    }
                }
            }
        }
        
        $command->skipWhitespace();
        if ($command->peekSingle() && $command->peekSingle() != ',') // Do we have more to read?
            $this->handleCommand($command, $lexer, $output, $stackEnd);
        return;
    }
    
    private static function flatten($pieces) {
        if (is_array($pieces) && count($pieces) == 1)
            return self::flatten(current($pieces));
        else
            return $pieces;
    }
    
}

class TemplateCompileException extends \Exception {
}