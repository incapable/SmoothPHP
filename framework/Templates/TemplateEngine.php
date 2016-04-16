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

use SmoothPHP\Framework\Cache\CacheProvider;

class TemplateEngine {
    private $compiler;

    private $compileCache;

    public function __construct() {
        $this->compiler = new TemplateCompiler();
        $this->compileCache = new CacheProvider('ctpl', 'ctpl',
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
    }

    public function fetch($templateName) {
        return $this->compileCache->fetch(sprintf('%s/src/templates/%s.tpl', __ROOT__, $templateName));
    }

}