<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * TemplateLexer.php
 * Lexer class that is capable of iterating through the provided source and lookaheads.
 */

namespace SmoothPHP\Framework\Templates;

class TemplateLexer {
    private $pointer;
    private $content;
    
    public function __construct($content) {
        $this->content = $content;
        $this->pointer = 0;
    }
    
    public function next() {
        if ($this->pointer == strlen($this->content))
            return false;
        return $this->content[$this->pointer++];
    }
    
    public function isAnyOf() {
        if ($this->pointer == strlen($this->content))
            return false;

        return in_array($this->content[$this->pointer], func_get_args());
    }
    
    public function isWhitespace() {
        return $this->isAnyOf(' ', "\n", "\r", "\t");
    }
    
    public function skipWhitespace() {
        while($this->isWhitespace())
            if (!$this->next())
                return;
    }
    
    public function peekSingle() {
        if ($this->pointer == strlen($this->content))
            return false;
        return $this->content[$this->pointer];
    }
    
    public function peek($compare) {
        $length = strlen($compare);
        if ( ( $this->pointer + $length ) > strlen($this->content) )
            return false;
        
        $characters = substr($this->content, $this->pointer, $length);
        if (strtolower($characters) === strtolower($compare)) {
            $this->pointer += $length;
            return true;
        } else
            return false;
    }
    
    public function remainder() {
        return substr($this->content, $this->pointer);
    }
}