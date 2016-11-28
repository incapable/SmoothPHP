<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * FormBuilder.php
 * Class responsible for creating 'Form' instances.
 */

namespace SmoothPHP\Framework\Forms;

use SmoothPHP\Framework\Forms\Containers\FormContainer;
use SmoothPHP\Framework\Forms\Containers\Type;
use SmoothPHP\Framework\Forms\Types as Types;

class FormBuilder {
    private $action = '';
    private $properties;

    /**
     * @param string $field of the field
     * @param string $type Type name of the field
     * @param array $attributes Properties to be used, such as Label
     * @return $this
     */
    public function add($field, $type = null, array $attributes = array()) {
        if (isset($this->properties[$field]))
            throw new \RuntimeException("Form field has already been declared.");

        $this->properties[$field] = array_merge_recursive(array(
            'field' => $field,
            'type' => $type ?: Types\StringType::class,
            'attr' => array()
        ), $attributes);

        return $this;
    }

    public function setAction() {
        global $kernel;
        $action = func_get_arg(0);

        if ($kernel->getRouteDatabase()->getRoute($action))
            $this->action = call_user_func_array(array($kernel->getRouteDatabase(), 'buildPath'), func_get_args());
        else
            $this->action = $action;
    }

    public function getForm() {
        $elements = array();

        foreach ($this->properties as $key => $value) {
            /* @var $element Type */
            $element = new $value['type']($key, $value);
            $element->buildConstraints();
            $elements[$key] = new FormContainer(array(
                'rowstart' => '<tr><td>',
                'label' => $element->generateLabel(),
                'rowseparator' => '</td><td>',
                'input' => $element,
                'rowend' => '</td></tr>'
            ));
        }

        return new Form($this->action, $elements);
    }
}