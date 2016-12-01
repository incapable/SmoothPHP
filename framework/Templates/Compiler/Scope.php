<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Scope.php
 * Class representing a programming scope, which will keep variables private unless previously declared
 */

namespace SmoothPHP\Framework\Templates\Compiler;

class Scope {
    private $parent;
    private $variables;

    public function __construct(Scope $parent = null) {
        $this->parent = $parent;
        $this->variables = array();
    }

    public function __set($name, $value) {
        if (!$this->setIfDeclared($name, $value)) {
            // var_dump('set', $name, $value);
            $this->variables[$name] = $value;
        }
    }

    private function setIfDeclared($name, $value) {
        if (isset($this->variables[$name])) {
            // var_dump('overwrite', $name, $value);
            $this->variables[$name] = $value;
            return true;
        } else {
            if ($this->parent == null)
                return false;
            else
                return $this->parent->setIfDeclared($name, $value);
        }
    }

    public function __get($name) {
        if (isset($this->variables[$name]))
            return $this->variables[$name];
        else if ($this->parent != null)
            return $this->parent->{$name};
        else
            return null;
    }

    public function __isset($name) {
        return $this->{$name} != null;
    }

}