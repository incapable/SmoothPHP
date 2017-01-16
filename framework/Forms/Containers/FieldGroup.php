<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * FieldGroup.php
 * Form element that lists several input types.
 */

namespace SmoothPHP\Framework\Forms\Containers;

use SmoothPHP\Framework\Flow\Requests\Request;
use SmoothPHP\Framework\Forms\Constraint;
use SmoothPHP\Framework\Forms\Types\StringType;

class FieldGroup extends Type {
    private $children;

    public function __construct($field) {
        parent::__construct($field);
        $this->attributes = array_replace_recursive($this->attributes, array(
            'children' => array()
        ));
    }

    public function initialize(array $attributes) {
        $this->attributes = array_merge_recursive($this->attributes, $attributes);
        $childAttributes = $this->attributes;
        unset($childAttributes['children']);

        $this->children = array();

        $first = true;
        foreach ($attributes['children'] as $value) {
            /* @var $element Type */
            $element = new $value['type']($value['field']);
            $element->initialize(array_merge_recursive($childAttributes, $value));
            $this->children[$value['field']] = new FormContainer(array(
                'groupseparator' => $first ? '' : '</td></tr><tr><td></td><td>',
                'input' => $element
            ));
            $first = false;
        }
    }

    public function getContainer() {
        return array(
            'rowstart' => '<tr><td>',
            'label' => $this->generateLabel(),
            'rowseparator' => '</td><td>',
            'children' => new FormContainer($this->children),
            'rowend' => '</td></tr>'
        );
    }

    public function checkConstraint(Request $request, $name, $value, array &$failReasons) {
        foreach($this->children as $element)
            if ($element instanceof Constraint) {
                if ($element instanceof Type)
                    $value = $request->post->get($element->getFieldName());
                $element->checkConstraint($request, null, $value, $failReasons);
            }
    }

    public static function child($field, $type = null, array $attributes = array()) {
        return array_merge_recursive(array(
            'field' => $field,
            'type' => $type ?: StringType::class,
            'attr' => array()
        ), $attributes);
    }

}