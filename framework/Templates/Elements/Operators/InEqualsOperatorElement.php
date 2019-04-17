<?php

/**
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * **********
 * Copyright © 2015-2019
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * **********
 * InEqualsOperatorElement.php
 */

namespace SmoothPHP\Framework\Templates\Elements\Operators;

use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\Compiler\TemplateCompileException;
use SmoothPHP\Framework\Templates\Compiler\TemplateLexer;
use SmoothPHP\Framework\Templates\Elements\Chain;
use SmoothPHP\Framework\Templates\Elements\Element;
use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;
use SmoothPHP\Framework\Templates\TemplateCompiler;

class InEqualsOperatorElement extends ArithmeticOperatorElement {

	protected function getPriority() {
		return 6;
	}

	public function optimize(CompilerState $tpl) {
		$left = $this->left->optimize($tpl);
		$right = $this->right->optimize($tpl);

		if ($left instanceof PrimitiveElement && $right instanceof PrimitiveElement)
			return new PrimitiveElement($left->getValue() != $right->getValue());
		else
			return new self($left, $right);
	}

	public function output(CompilerState $tpl) {
		$result = $this->optimize($tpl);

		if (!($result instanceof PrimitiveElement))
			throw new TemplateCompileException("Could not arithmetic values at runtime.");

		$result->output($tpl);
	}

}
