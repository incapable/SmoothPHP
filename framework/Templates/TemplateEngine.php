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
        return $this->compiler->compile(__ROOT__ . '/src/templates/' . $templateName . '.tpl');
    }
    
}