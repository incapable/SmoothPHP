<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * FileSizeConstraint.php
 * Constraint for forms that require a file size limitation.
 */

namespace SmoothPHP\Framework\Forms\Constraints;

use SmoothPHP\Framework\Forms\Constraint;
use SmoothPHP\Framework\Flow\Requests\Request;
use SmoothPHP\Framework\Forms\Form;

class IdenticalConstraint extends Constraint {
	private $fieldName;

	public function __construct($fieldName) {
		$this->fieldName = $fieldName;
	}

	public function setOptions(array &$options) {
	}

	public function checkConstraint(Request $request, $name, $label, $value, Form $form) {
		if (!$form->hasField($this->fieldName))
			throw new \RuntimeException('Attempted to compare form field but that field does not exist.');

		if ($value != $request->post->{$this->fieldName})
			$form->addErrorMessage('');
	}

}