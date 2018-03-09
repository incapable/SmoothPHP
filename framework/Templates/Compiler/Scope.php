<?php

/**
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * **********
 * Copyright © 2015-2018
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * **********
 * Scope.php
 */

namespace SmoothPHP\Framework\Templates\Compiler;

class Scope {
	private $parent;
	private $variables;

	public function __construct(Scope $parent = null) {
		$this->parent = $parent;
		$this->variables = [];
	}

	public function __set($name, $value) {
		if (!$this->setIfDeclared($name, $value)) {
			$this->variables[$name] = $value;
		}
	}

	private function setIfDeclared($name, $value) {
		if (isset($this->variables[$name])) {
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