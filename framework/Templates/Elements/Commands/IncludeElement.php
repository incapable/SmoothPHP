<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * IncludeElement.php
 * Includes another template file on the current position.
 */

namespace SmoothPHP\Framework\Templates\Elements\Commands;

use SmoothPHP\Framework\Cache\CacheExpiredException;
use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\Compiler\PHPBuilder;
use SmoothPHP\Framework\Templates\Compiler\TemplateCompileException;
use SmoothPHP\Framework\Templates\Compiler\TemplateLexer;
use SmoothPHP\Framework\Templates\Elements\Chain;
use SmoothPHP\Framework\Templates\Elements\Element;
use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;
use SmoothPHP\Framework\Templates\TemplateCompiler;

class IncludeElement extends Element {
    private $file;
    private $md5;

    public static function handle(TemplateCompiler $compiler, TemplateLexer $command, TemplateLexer $lexer, Chain $chain) {
        $args = new Chain();
        $compiler->handleCommand($command, $lexer, $args);
        $args = $args->getAll();

        if (!($args[0] instanceof PrimitiveElement))
            throw new TemplateCompileException("Include file path could not be resolved at parse time.");

        $path = sprintf('%ssrc/templates/%s', __ROOT__, $args[0]->getValue());

        $include = new Chain();
        $compiler->read(new TemplateLexer(file_get_contents($path)), $include);

        $chain->addElement(new self($path));
        $chain->addElement($include);
    }

    public function __construct($file) {
        $this->file = $file;
        $this->md5 = md5_file($file);
    }

    public function optimize(CompilerState $tpl) {
        return $this;
    }

    public function writePHP(PHPBuilder $php) {
        if (md5_file($this->file) != $this->md5)
            throw new CacheExpiredException();

        $php->openPHP();
        $php->append(sprintf('$_smooth_tpl->verify_cache(\'%s\', \'%s\');', $this->file, $this->md5));
    }

}