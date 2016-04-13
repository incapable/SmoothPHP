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
            $trimmed = trim($rawString);
            if (strlen($trimmed) > 0)
                $output[] = new Elements\StringElement($trimmed);
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
   
    private function handleCommand(TemplateLexer $command, TemplateLexer $lexer, array &$output) {
        if ($command->peek('(')) {
            $elements = array();
            $this->handleCommand(new TemplateLexer($this->readRaw($command, ')')), $lexer, $elements);
            $output[] = new Elements\ParenthesisElement($elements);
        } else if ($command->peek('$')) {
            $output[] = new Elements\VariableElement($this->readAlphaNumeric($command));
        } else {
            $commandName = strtolower($this->readAlphaNumeric($command));
            if (strlen($commandName) == 0)
                return;

            switch($commandName) {
                case 'assign':
                    $command->skipWhitespace();
                    $command->peek('$');
                    $varName = $this->readAlphaNumeric($command);

                    $command->skipWhitespace();
                    $value = array();
                    $this->handleCommand($command, $lexer, $value);

                    $output[] = new Elements\AssignElement($varName, $value);
                    return;
                case 'if':
                    $condition = array();
                    $this->handleCommand($command, $lexer, $condition);
                    $body = array();
                    $this->read($lexer, $body);
                    $output[] = new Elements\ParenthesisElement($body);
                    break;
                default:
                    // Handle constant value
                    // throw new TemplateCompileException("Unknown command '" . $commandName . "'.");
            }
        }
    }
    
}

class TemplateCompileException extends \Exception {
}