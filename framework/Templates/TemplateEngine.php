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

class TemplateEngine {
    private $compiler;
    
    public function __construct() {
        $this->compiler = new TemplateCompiler();
    }
    
    public function fetch($templateName) {
        $file = __ROOT__ . '/src/templates/' . $templateName . '.tpl';
        $md5 = md5_file($file);
        $dir = __ROOT__ . '/cache/ctpl/';
        $cache = $dir . $templateName . '.' . $md5 . '.ctpl';
        
        if (!is_dir($dir))
            mkdir($dir, 0755, true);
        
        if (file_exists($cache))
            return unserialize(gzinflate(file_get_contents($cache)));
        else {
            foreach(glob($dir . $templateName . '.*.ctpl') as $old)
                unlink($old);
            $ctpl = $this->compiler->compile($file);
            file_put_contents($cache, gzdeflate(serialize($ctpl)));
            return $ctpl;
        }
    }
    
}