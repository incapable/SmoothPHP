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

namespace SmoothPHP\Framework\Templates\Compiler;

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
        while ($this->isWhitespace())
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
        if (($this->pointer + $length) > strlen($this->content))
            return false;

        $characters = substr($this->content, $this->pointer, $length);
        if (strtolower($characters) === strtolower($compare)) {
            $this->pointer += $length;
            return true;
        } else
            return false;
    }

    public function readAlphaNumeric() {
        $rawString = '';

        while (true) {
            $char = $this->peekSingle();
            if (ctype_alnum($char) || $char === '_')
                $rawString .= $this->content[$this->pointer++];
            else
                return $rawString;
        }
    }

    public function readRaw($stackEnd = null, $stackEscape = null) {
        $rawString = '';

        while (true) {
            if ($stackEscape != null && $this->peek($stackEscape)) {
                $rawString .= $stackEnd;
            } else if ($stackEnd != null && $this->peek($stackEnd)) {
                return $rawString;
            } else {
                $char = $this->next();
                if ($char !== false)
                    $rawString .= $char;
                else
                    return $rawString;
            }
        }
    }

    public function remainder() {
        return substr($this->content, $this->pointer);
    }
}