<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * InEqualsOperatorElement.php
 * Inequals operator, returns a boolean value representing what the value does not evaluate to
 */

namespace SmoothPHP\Framework\Templates\Elements\Operators;

use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\Compiler\TemplateCompileException;
use SmoothPHP\Framework\Templates\Compiler\TemplateLexer;
use SmoothPHP\Framework\Templates\Elements\Chain;
use SmoothPHP\Framework\Templates\Elements\Element;
use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;
use SmoothPHP\Framework\Templates\TemplateCompiler;

class InEqualsOperatorElement extends Element {
	private $left, $right;

	public static function handle(TemplateCompiler $compiler, TemplateLexer $command, TemplateLexer $lexer, Chain $chain, $stackEnd) {
		$command->next();
		$isEqualsOperator = $command->peek('=');
		$right = new Chain();
		$compiler->handleCommand($command, $lexer, $right, $stackEnd);
		if ($isEqualsOperator)
			$chain->addElement(new self($chain->pop(), TemplateCompiler::flatten($right)));
		else
			$chain->addElement(new InverseOperatorElement(TemplateCompiler::flatten($right)));
	}

	public function __construct(Element $left, Element $right) {
		$this->left = $left;
		$this->right = $right;
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
