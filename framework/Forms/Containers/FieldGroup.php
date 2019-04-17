<?php

/**
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * **********
 * Copyright © 2015-2019
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * **********
 * FieldGroup.php
 */

namespace SmoothPHP\Framework\Forms\Containers;

use SmoothPHP\Framework\Flow\Requests\Request;
use SmoothPHP\Framework\Forms\Constraint;
use SmoothPHP\Framework\Forms\Form;
use SmoothPHP\Framework\Forms\Types\StringType;

class FieldGroup extends Type {
	private $children;

	public function __construct($field) {
		parent::__construct($field);
		$this->options = array_replace_recursive($this->options, [
			'children' => []
		]);
	}

	public function initialize(array $options) {
		$this->options = array_merge_recursive($this->options, $options);
		$childOptions = $this->options;
		unset($childOptions['children']);
		unset($childOptions['required']);

		$this->children = [];

		$first = true;
		foreach ($options['children'] as $value) {
			/* @var $element Type */
			$element = new $value['type']($value['field']);
			$element->initialize(array_merge_recursive($childOptions, $value));
			$this->children[$value['field']] = new FormContainer([
				'groupseparator' => $first ? '' : sprintf('</td></tr><tr class="fieldgroup_%s"><td></td><td>', $this->field),
				'input'          => $element
			]);
			$first = false;
		}
	}

	public function &getChild($key) {
		return $this->children[$key];
	}

	public function getContainer() {
		return [
			'rowstart'     => sprintf('<tr class="fieldgroup_%s"><td>', $this->field),
			'label'        => $this->generateLabel(),
			'rowseparator' => '</td><td>',
			'children'     => new FormContainer($this->children),
			'rowend'       => '</td></tr>'
		];
	}

	public function checkConstraint(Request $request, $name, $label, $value, Form $form) {
		foreach ($this->children as $element)
			if ($element instanceof Constraint) {
				if ($element instanceof Type) {
					$name = $element->getFieldName();
					$value = $request->post->get($element->getFieldName());
				} else {
					$name = null;
					$value = null; // Because PHP doesn't respect scope
				}
				$element->checkConstraint($request, $name, null, $value, $form);
			}
	}

	public static function child($field, $type = null, array $attributes = []) {
		return array_merge_recursive([
			'field' => $field,
			'type'  => $type ?: StringType::class,
			'attr'  => []
		], $attributes);
	}

}