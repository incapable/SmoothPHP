<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * CustomType.php
 * Custom input field
 */

namespace SmoothPHP\Framework\Forms\Types;

use SmoothPHP\Framework\Flow\Requests\Request;
use SmoothPHP\Framework\Forms\Containers\Type;

class CustomType extends Type {

	public function __construct($field) {
		parent::__construct($field);
		$this->attributes = array_replace_recursive($this->attributes, [
			'required' => false,
			'content'  => '<i>content</i> parameter not set'
		]);
	}

	public function checkConstraint(Request $request, $name, $label, $value, array &$failReasons) {
	}

	public function __toString() {
		return last($this->attributes['content']);
	}

}