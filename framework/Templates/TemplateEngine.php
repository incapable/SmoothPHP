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
use SmoothPHP\Framework\Templates\Compiler\PHPBuilder;

class TemplateEngine {
    private $compiler;

    private $compileCache;
    private $phpCache;

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
        $this->phpCache = new CacheProvider('tplcache_php', 'php');
    }

    public function fetch($templateName) {
        $path = sprintf('%ssrc/templates/%s', __ROOT__, $templateName);
        return $this->phpCache->fetch($path,
            function () use ($path) {
                $doc = new PHPBuilder();
                $this->compileCache->fetch($path)->writePHP($doc);
                $doc->closePHP();
                return $doc->getPHP();
            }
        );
    }

}