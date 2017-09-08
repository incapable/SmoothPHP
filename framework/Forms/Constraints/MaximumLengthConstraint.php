<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * MaximumLengthConstraint.php
 * Constraint for forms that requires this particular field to have a maximum length.
 */

namespace SmoothPHP\Framework\Forms\Constraints;

use SmoothPHP\Framework\Forms\Constraint;
use SmoothPHP\Framework\Flow\Requests\Request;
use SmoothPHP\Framework\Forms\Form;

class MaximumLengthConstraint extends Constraint {
	private $maxLength;

	public function __construct($maxLength = 0) {
		$this->maxLength = $maxLength;
	}

	public function setOptions(array &$options) {
		$options['attr']['maxlength'] = $this->maxLength;
	}

	public function checkConstraint(Request $request, $name, $label, $value, Form $form) {
		if (strlen($value) > $this->maxLength) {
			global $kernel;
			$form->addErrorMessage(sprintf($kernel->getLanguageRepository()->getEntry('smooth_form_maxlength'), $label, $this->maxLength));
		}
	}

}