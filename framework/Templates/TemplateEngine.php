<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * TemplateEngine.php
 * Template engine, responsible for invoking the compiler, caching and returning the output page
 */

namespace SmoothPHP\Framework\Templates;

use SmoothPHP\Framework\Cache\Builder\FileCacheProvider;
use SmoothPHP\Framework\Cache\Builder\RuntimeCacheProvider;
use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;

class TemplateEngine {
    private $compiler;

    private $runtimeCache;

    public function __construct() {
        $this->compiler = new TemplateCompiler();

        $compileCache = new FileCacheProvider('ctpl', 'ctpl',
            function ($fileName) {
                return $this->compiler->compile($fileName);
            },
            function ($fileName) {
                return unserialize(gzinflate(file_get_contents($fileName)));
            },
            function ($fileName, $data) {
                file_put_contents($fileName, gzdeflate(serialize($data)));
            }
        );
        $this->runtimeCache = RuntimeCacheProvider::create(function ($fileName) use ($compileCache) {
            return $compileCache->fetch($fileName);
        });
    }

    public function fetch($templateName, array $args) {
        $path = sprintf('%ssrc/templates/%s', __ROOT__, $templateName);
        $template = $this->runtimeCache->fetch($path);

        $state = new CompilerState();
        $state->performCalls = true;
        $state->vars = array_map(function ($arg) {
            return new PrimitiveElement($arg);
        }, $args);

        ob_start();
        $template->output($state);
        return ob_get_clean();
    }

}