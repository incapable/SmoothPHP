<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * CSSElement.php
 * A template block that is later replaced by all used CSS files.
 */

namespace SmoothPHP\Framework\Cache\Assets\Template;

use SmoothPHP\Framework\Cache\Assets\AssetsRegister;
use SmoothPHP\Framework\Core\Lock;
use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\Compiler\TemplateLexer;
use SmoothPHP\Framework\Templates\Elements\Chain;
use SmoothPHP\Framework\Templates\Elements\Element;
use SmoothPHP\Framework\Templates\TemplateCompiler;
use tubalmartin\CSSmin\CSSmin;

class CSSElement extends Element {
    const FORMAT = '<link rel="stylesheet" type="text/css" href="%s" />';
    const COMPILED_PATH = __ROOT__ . 'cache/css/compiled.%s.css';

    public static function handle(TemplateCompiler $compiler, TemplateLexer $command, TemplateLexer $lexer, Chain $chain, $stackEnd) {
        if (__ENV__ == 'dev')
            $chain->addElement(new DebugCSSElement());
        else
            $chain->addElement(new self());
    }

    public function optimize(CompilerState $tpl) {
        return $this;
    }

    public function output(CompilerState $tpl) {
        /* @var $assetsRegister \SmoothPHP\Framework\Cache\Assets\AssetsRegister */
        $assetsRegister = $tpl->vars->assets->getValue();

        $files = array();

        foreach (array_unique($assetsRegister->getCSSFiles()) as $css) {
            if (strtolower(substr($css, 0, 4)) == 'http') {
                echo sprintf(self::FORMAT, $css);
                continue;
            }

            $files[] = $css;
        }

        $hash = md5(implode(',', $files));

        if (!file_exists(sprintf(self::COMPILED_PATH, $hash))) {
            $lock = new Lock('compiled.css.' . $hash);

            if ($lock->lock()) {
                $contents = '';
                array_walk($files, function ($file) use ($assetsRegister, &$contents) {
                    $contents .= ' ' . file_get_contents($assetsRegister->getCSSPath($file));
                });

                $cssmin = new CSSmin();
                $optimized = $cssmin->run($contents);
                file_put_contents(sprintf(self::COMPILED_PATH, $hash), $optimized);
            }
        }

        echo sprintf(self::FORMAT, '/css/' . $hash . '/compiled.css');
    }

}