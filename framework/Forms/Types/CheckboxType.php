<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * CheckboxType.php
 * Type for html's input[type="checkbox"]
 */

namespace SmoothPHP\Framework\Forms\Types;

use SmoothPHP\Framework\Forms\Containers\Type;

class CheckboxType extends Type {

	public function __construct($field) {
		parent::__construct($field);
		$this->attributes = array_replace_recursive($this->attributes, [
			'attr'       => [
				'type' => 'checkbox'
			],
			'required'   => false,
			'mergelabel' => true
		]);
	}

	public function generateLabel() {
		if (last($this->attributes['mergelabel']))
			return '';
		else
			return parent::generateLabel();
	}

	public function __toString() {
		if (isset($this->attributes['attr']['value']) && last($this->attributes['attr']['value'])) {
			$this->attributes['attr']['checked'] = 'checked';
			unset($this->attributes['attr']['value']);
		}

		if (last($this->attributes['mergelabel']))
			return sprintf('<label>%s %s</label>', parent::__toString(), last($this->attributes['label']));
		else
			return parent::__toString();
	}

}