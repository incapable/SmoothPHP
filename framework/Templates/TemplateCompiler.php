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
    const DELIMITER_START = '{[';
    const DELIMITER_END = ']}';
    
    public function compile($file) {
        $lexer = new TemplateLexer(file_get_contents($file));
        $output = '';
        $this->read($lexer, $output);
        return $output;
    }
    
    private function read(TemplateLexer $lexer, &$output, $upto = null) {
        $continue = true;
        while($continue) {
            if ($upto != null && $lexer->lookAhead($upto))
                return;
            else if ($lexer->lookAhead(self::DELIMITER_START)) {
                $command = '';
                $this->read($lexer, $command, self::DELIMITER_END);
                $this->parseCommand($lexer, $command, $output);
            } else {
                $next = $lexer->getNext();
                if ($next)
                    $output .= $next;
                else
                    return;
            }
        }
    }
    
    private function parseCommand(TemplateLexer $lexer, $command, &$output) {
        if ($command[0] == '=') // Language shortcut
            $output .= '<?php echo ' . substr($command, 1) . '; ?>';
        else {
            $split = strpos($command, ' ') ?: strlen($command);
            $commandName = substr($command, 0, $split);
            switch(strtolower($commandName)) {
                case "assign":
                    $assignment = explode('=', substr($command, $split + 1));
                    $output .= '<?php ' . $assignment[0] . ' = ' . $assignment[1] . '; ?>';
                    break;
                case "if":
                    $this->read($lexer, $ifBlock, self::DELIMITER_START . '/if' . self::DELIMITER_END);
                    $output .= '<?php if (' . substr($command, $split + 1) . ') {?>' . $ifBlock . '<?php } ?>';
                    break;
                default:
                    throw new TemplateCompileException("Unknown operator: " . $commandName);
            }
        }
    }
    
}

class TemplateLexer {
    private $pointer;
    private $content;
    
    public function __construct($content) {
        $this->content = $content;
        $this->pointer = 0;
    }
    
    public function getNext() {
        if ($this->pointer > (strlen($this->content) - 1))
            return false;
        return $this->content[$this->pointer++];
    }
    
    public function lookAhead($str) {
        $peek = strtolower(substr($this->content, $this->pointer, strlen($str)));
        if ($peek == $str) {
            $this->pointer += strlen($str);
            return true;
        } else
            return false;
    }
}

class TemplateCompileException extends \Exception {
}