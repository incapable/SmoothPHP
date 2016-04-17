<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * IncludeElement.php
 * Description
 */

namespace SmoothPHP\Framework\Templates\Elements\Commands;

use SmoothPHP\Framework\Templates\Compiler\TemplateCompileException;
use SmoothPHP\Framework\Templates\Compiler\TemplateLexer;
use SmoothPHP\Framework\Templates\Elements\Chain;
use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;
use SmoothPHP\Framework\Templates\TemplateCompiler;

class IncludeElement {

    public static function handle(TemplateCompiler $compiler, TemplateLexer $command, TemplateLexer $lexer, Chain $chain) {
        $args = new Chain();
        $compiler->handleCommand($command, $lexer, $args);
        $args = $args->getAll();

        if (!($args[0] instanceof PrimitiveElement))
            throw new TemplateCompileException("Include file path could not be resolved at parse time.");

        $path = sprintf('%s/src/templates/%s.tpl', __ROOT__, $args[0]->getValue());

        $include = new Chain();
        $compiler->read(new TemplateLexer(file_get_contents($path)), $include);
        $chain->addElement($include);
    }

}