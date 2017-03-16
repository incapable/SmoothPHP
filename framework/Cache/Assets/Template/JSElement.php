<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * JSElement.php
 * A template block that is later replaced by all used JavaScript files.
 */

namespace SmoothPHP\Framework\Cache\Assets\Template;

use JShrink\Minifier;
use SmoothPHP\Framework\Core\Lock;
use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\Compiler\TemplateLexer;
use SmoothPHP\Framework\Templates\Elements\Chain;
use SmoothPHP\Framework\Templates\Elements\Element;
use SmoothPHP\Framework\Templates\TemplateCompiler;

class JSElement extends Element {
    const FORMAT = '<script type="text/javascript" src="%s"></script>';
    const COMPILED_PATH = __ROOT__ . 'cache/js/compiled.%s.js';

    public static function handle(TemplateCompiler $compiler, TemplateLexer $command, TemplateLexer $lexer, Chain $chain, $stackEnd) {
        if (__ENV__ == 'dev')
            $chain->addElement(new DebugJSElement());
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

        foreach (array_unique($assetsRegister->getJSFiles()) as $js) {
            if (strtolower(substr($js, 0, 4)) == 'http') {
                echo sprintf(self::FORMAT, $js);
                continue;
            }

            $files[] = $assetsRegister->getJSPath($js);
        }

        if (count($files) == 0)
            return;

        $hash = md5(implode(',', $files));

        if (!file_exists(sprintf(self::COMPILED_PATH, $hash))) {
            $lock = new Lock('compiled.js.' . $hash);

            if ($lock->lock()) {
                $contents = '';
                array_walk($files, function ($file) use ($assetsRegister, &$contents) {
                    $contents .= '; ' . file_get_contents($file);
                });

                $optimized = Minifier::minify($contents);
                file_put_contents(sprintf(self::COMPILED_PATH, $hash), $optimized);
            }
        }

        echo sprintf(self::FORMAT, '/js/' . $hash . '/compiled.js');
    }
}
