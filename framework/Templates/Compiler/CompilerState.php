<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * CompilerState.php
 * Struct-like class that represents a scope in the template.
 */

namespace SmoothPHP\Framework\Templates\Compiler;

class CompilerState {
    public $vars;
    public $blocks;
    public $uncertainVars;
    public $finishing;
    public $performCalls;

    public function __construct() {
        $this->vars = new Scope();
        $this->blocks = array();
        $this->uncertainVars = false;
        $this->finishing = false;
        $this->performCalls = false;
    }

    public function createSubScope() {
        $copy = new self();

        $copy->vars = new Scope($this->vars);
        $copy->blocks = $this->blocks;
        $copy->finishing = $this->finishing;
        $copy->performCalls = $this->performCalls;

        return $copy;
    }
}
