<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * FileImageMaximumSizeConstraint.php
 * Constraint for file image uploads that require certain maximum image dimensions.
 */

namespace SmoothPHP\Framework\Forms\Constraints;

use SmoothPHP\Framework\Forms\Constraint;
use SmoothPHP\Framework\Flow\Requests\Request;
use SmoothPHP\Framework\Forms\Form;

class FileImageMaximumSizeConstraint extends Constraint {
	private $width, $height;

	public function __construct($width, $height) {
		$this->width = $width;
		$this->height = $height;
	}

	public function setOptions(array &$options) {
	}

	public function checkConstraint(Request $request, $name, $label, $value, Form $form) {
		$imageSize = getimagesize($request->files->{$name}->location);
		if ($imageSize === false) {
			global $kernel;
			$form->addErrorMessage(sprintf($kernel->getLanguageRepository()->getEntry('smooth_form_file_image_invalid'), $label));
			return;
		}

		if ($imageSize[0] > $this->width || $imageSize[1] > $this->height) {
			global $kernel;
			$form->addErrorMessage(sprintf($kernel->getLanguageRepository()->getEntry('smooth_form_file_image_toolarge'), $label, $this->width, $this->height));
		}
	}

}