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
		$this->options = array_replace_recursive($this->options, [
			'attr'       => [
				'type' => 'checkbox'
			],
			'required'   => false,
			'mergelabel' => true
		]);
	}

	public function generateLabel() {
		if (last($this->options['mergelabel']))
			return '';
		else
			return parent::generateLabel();
	}

	public function __toString() {
		if (isset($this->options['attr']['value']) && last($this->options['attr']['value'])) {
			$this->options['attr']['checked'] = 'checked';
			unset($this->options['attr']['value']);
		}

		if (last($this->options['mergelabel']))
			return sprintf('<label>%s %s</label>', parent::__toString(), last($this->options['label']));
		else
			return parent::__toString();
	}

}