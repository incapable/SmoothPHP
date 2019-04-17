<?php

/**
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * **********
 * Copyright © 2015-2019
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * **********
 * DereferenceOperatorElement.php
 */

namespace SmoothPHP\Framework\Templates\Elements\Operators;

use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\Compiler\TemplateCompileException;
use SmoothPHP\Framework\Templates\Elements\Commands\VariableElement;
use SmoothPHP\Framework\Templates\Elements\Element;
use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;

class DereferenceOperatorElement extends Element {
	private $left, $right;

	public function __construct(Element $left, Element $right = null) {
		$this->left = $left;
		$this->right = $right;
	}

	public function setRight(Element $right) {
		$this->right = $right;
	}

	public function optimize(CompilerState $tpl) {
		$left = $this->left->optimize($tpl);
		$right = $this->right;

		if ($tpl->performCalls && $left instanceof PrimitiveElement && $right instanceof PrimitiveElement)
			if (is_array($left->getValue()))
				return new PrimitiveElement($left->getValue()[$right->getValue()]);
			else
				return new PrimitiveElement($left->getValue()->{$right->getValue()});

		if ($right instanceof FunctionOperatorElement && $tpl->performCalls) {
			if ($left instanceof VariableElement) {
				if (!$tpl->isUncertain())
					throw new TemplateCompileException(sprintf("Template variable '%s' is not defined.", $left->getVarName()));
			} else
				return new PrimitiveElement(call_user_func_array([$left->getValue(), $right->getFunctionName()], $right->getPrimitiveArgs($tpl)));
		} else
			$right = $right->optimize($tpl);

		return new self($left, $right);
	}

	public function output(CompilerState $tpl) {
		$optimized = $this->optimize($tpl);

		if (!($optimized->left instanceof PrimitiveElement))
			throw new TemplateCompileException("Could not determine left-hand of '->' at runtime.");
		else {
			if ($optimized->right instanceof PrimitiveElement)
				if (is_array($optimized->left->getValue()))
					echo $optimized->left->getValue()[$optimized->right->getValue()];
				else
					echo $optimized->left->getValue()->{$optimized->right->getValue()};
			else if ($optimized->right instanceof FunctionOperatorElement)
				echo call_user_func_array([$optimized->left->getValue(), $optimized->right->getFunctionName()], $optimized->right->getPrimitiveArgs($tpl));
			else
				throw new TemplateCompileException("Right-hand of '->' is invalid.");
		}
	}
}
