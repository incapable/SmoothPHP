<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * StringElement.php
 * Template element of a 'raw' string.
 */

namespace SmoothPHP\Framework\Templates\Elements;

use SmoothPHP\Framework\Templates\Compiler\TemplateLexer;
use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\TemplateCompiler;
use SmoothPHP\Framework\Templates\Compiler\PHPBuilder;

class PrimitiveElement extends Element {
    private $value;

    public static function handle(TemplateCompiler $compiler, TemplateLexer $command, TemplateLexer $lexer, Chain $chain) {
        $strStart = $command->next();
        $str = $command->readRaw($strStart, '\\' . $strStart);
        $chain->addElement(new self($str));
    }

    public function __construct($value = '', $tryParse = false) {
        if ($tryParse && is_string($value)) {
            if (is_numeric($value))
                $value = $value + 0;
            else {
                switch ($value) {
                    case "true":
                        $value = true;
                        break;
                    case "false":
                        $value = false;
                        break;
                }
            }
        }
        $this->value = $value;
    }

    public function getValue() {
        return $this->value;
    }

    public function optimize(CompilerState $tpl) {
        return $this;
    }

    public function writePHP(PHPBuilder $php) {
        $value = $this->value;
        if ($php->isPHPTagOpen())
            $value = is_string($value) ? sprintf("'%s'", str_replace('\'', '\\\'', $value)) : $value;
        $php->append($value);
    }

}
