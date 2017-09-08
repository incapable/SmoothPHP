<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * MinimumLengthConstraint.php
 * Constraint for forms that requires this particular field to have a minimum length.
 */

namespace SmoothPHP\Framework\Forms\Constraints;

use SmoothPHP\Framework\Forms\Constraint;
use SmoothPHP\Framework\Flow\Requests\Request;
use SmoothPHP\Framework\Forms\Form;

class MinimumLengthConstraint extends Constraint {
	private $minLength;

	public function __construct($minLength = 0) {
		$this->minLength = $minLength;
	}

	public function setOptions(array &$options) {
		$options['attr']['minlength'] = $this->minLength;
	}

	public function checkConstraint(Request $request, $name, $label, $value, Form $form) {
		if (strlen($value) < $this->minLength) {
			global $kernel;
			$form->addErrorMessage(sprintf($kernel->getLanguageRepository()->getEntry('smooth_form_minlength'), $label, $this->minLength));
		}
	}

}