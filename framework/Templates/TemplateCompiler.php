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

class TemplateCompiler {
    const DELIMITER_START = '{';
    const DELIMITER_END = '}';
    
    public function compile($file) {
        $lexer = new TemplateLexer(file_get_contents($file));
        $output = array();
        $this->read($lexer, $output);
        return $output;
    }
    
    private function read(TemplateLexer $lexer, array &$output, $stackEnd = null) {
        $rawString = '';
        
        $finishString = function() use (&$output, &$rawString) {
            if (strlen(trim($rawString)) > 0)
                $output[] = new Elements\StringElement($rawString);
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
                    if ($char)
                        $command .= $char;
                    else
                        throw new TemplateCompileException("Template syntax error, unfinished command: " . self::DELIMITER_START . $command);
                }
                
                $finishString();
                
                $this->handleCommand(new TemplateLexer($command), $lexer, $output);
            } else {
                $char = $lexer->next();
                if ($char)
                    $rawString .= $char;
                else {
                    $finishString();
                    return;
                }
            }
        }
    }
    
    private function readRaw(TemplateLexer $lexer, $stackEnd = null) {
        $rawString = '';
        
        while(true) {
            if ($stackEnd != null && $lexer->peek($stackEnd)) {
                return $rawString;
            } else {
                $char = $lexer->next();
                if ($char)
                    $rawString .= $char;
                else
                    return $rawString;
            }
        }
    }
    
    private function readAlphaNumeric(TemplateLexer $lexer) {
        $rawString = '';
        
        while(true) {
            $char = $lexer->peekSingle();
            if (ctype_alnum($char))
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
            $output[] = new Elements\ParenthesisElement($elements);
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

                    $output[] = new Elements\Commands\AssignElement($varName, $value);
                    return;
                }
                case 'if': {
                    $condition = array();
                    $this->handleCommand($command, $lexer, $condition, $stackEnd);
                    $body = array();
                    $this->read($lexer, $body, '{/if}');
                    $output[] = new Elements\Commands\IfElement($condition, $body);
                    break;
                }
                default: {
                    if (strlen(trim($next)) > 0)
                        $output[] = new Elements\StringElement($next);
                    else {
                        $command->skipWhitespace();
                        switch($command->peekSingle()) {
                            case '+':
                                $command->next();
                                $command->skipWhitespace();

                                $right = array();
                                $this->handleCommand($command, $lexer, $right, $stackEnd);
                                $output[] = new Elements\Operators\PlusOperatorElement(array_pop($output), $right);
                                break;
                            case '=':
                                $command->next();
                                if ($command->peek('=')) {
                                    $command->next();
                                    
                                    $right = array();
                                    $this->handleCommand($command, $lexer, $right, $stackEnd);
                                    $output[] = new Elements\Operators\EqualsOperatorElement(array_pop($output), $right);
                                } else {
                                    $assignTo = array_pop($output);
                                    if (!($assignTo instanceof Elements\Commands\VariableElement))
                                        throw new TemplateCompileException("Attempting to assign a value to a non-variable.");
                                    
                                    $right = array();
                                    $this->handleCommand($command, $lexer, $right, $stackEnd);
                                    $output[] = new Elements\Commands\AssignElement($assignTo->getVarName(), $right);
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
        
        if ($command->peekSingle()) // Do we have more to read?
            $this->handleCommand($command, $lexer, $output, $stackEnd);
    }
    
}

class TemplateCompileException extends \Exception {
}