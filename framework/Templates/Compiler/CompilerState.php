<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * CompilerState.php
 * Struct-like class that represents a scope in the template.
 */

namespace SmoothPHP\Framework\Templates\Compiler;

class CompilerState {
    public $vars;
    public $blocks;
    public $uncertainDepth;
    public $finishing;
    public $performCalls;
    public $allowMinify;

    public function __construct() {
        $this->vars = new Scope();
        $this->blocks = array();
        $this->uncertainVars = 0;
        $this->finishing = false;
        $this->performCalls = false;
        $this->allowMinify = false;
    }

    public function createSubScope() {
        $copy = new self();

        $copy->vars = new Scope($this->vars);
        $copy->blocks = $this->blocks;
        $copy->finishing = $this->finishing;
        $copy->performCalls = $this->performCalls;
        $copy->allowMinify = $this->allowMinify;

        return $copy;
    }

    public function pushUncertainty() {
        $this->uncertainVars++;
    }

    public function popUncertainty() {
        $this->uncertainVars--;
    }

    public function isUncertain() {
        return $this->uncertainVars != 0;
    }
}
