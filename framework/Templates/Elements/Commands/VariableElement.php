<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * VariableElement.php
 * Element that outputs the current value of a variable.
 */

namespace SmoothPHP\Framework\Templates\Elements\Commands;

use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\Elements\Element;

class VariableElement extends Element {
	private $varName;

	public function __construct($varName) {
		$this->varName = $varName;
	}

	public function getVarName() {
		return $this->varName;
	}

	public function optimize(CompilerState $tpl) {
		if ($tpl->isUncertain())
			return $this;

		$var = $tpl->vars->{$this->varName};
		if ($var != null)
			return $var;
		else
			return $this;
	}

	public function output(CompilerState $tpl) {
		$tpl->vars->{$this->varName}->output($tpl);
	}
}
