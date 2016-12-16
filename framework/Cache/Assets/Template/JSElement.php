<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * JSElement.php
 * A template block that is later replaced by all used JavaScript files.
 */

namespace SmoothPHP\Framework\Cache\Assets\Template;

use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\Compiler\TemplateLexer;
use SmoothPHP\Framework\Templates\Elements\Chain;
use SmoothPHP\Framework\Templates\Elements\Element;
use SmoothPHP\Framework\Templates\TemplateCompiler;

class JSElement extends Element {

    public static function handle(TemplateCompiler $compiler, TemplateLexer $command, TemplateLexer $lexer, Chain $chain, $stackEnd) {
        $chain->addElement(new self());
    }

    public function optimize(CompilerState $tpl) {
        return $this;
    }

    public function output(CompilerState $tpl) {
        /* @var $assetHandler \SmoothPHP\Framework\Cache\Assets\AssetsRegister */
        $assetHandler = $tpl->vars->assets->getValue();
        foreach ($assetHandler->getJSFiles() as $js) {
            if (strtolower(substr($js, 0, 4)) != 'http')
                $js = '/js/' . $js;
            echo '<script type="text/javascript" src="' . $js . '"></script>';
        }
    }

}
