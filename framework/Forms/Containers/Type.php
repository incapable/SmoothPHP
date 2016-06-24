<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Type.php
 * Description
 */

namespace SmoothPHP\Framework\Forms\Containers;

abstract class Type {
    protected $field;
    protected $attributes;

    public function __construct($field, array $attributes = array()) {
        $this->field = $field;
        $this->attributes = array_replace_recursive(array(
            'label' => self::getLabel($field),
            'attr' => array(
                'class' => ''
            )
        ), $attributes);
    }

    public function generateLabel() {
        return sprintf('<label for="%s">%s</label>',
            $this->field,
            $this->attributes['label']);
    }

    public function __toString() {
        $htmlAttributes = array();
        $attributes = $this->attributes['attr'];

        $attributes['id'] = $this->field;
        $attributes['name'] = $this->field;

        foreach($attributes as $key => $attribute)
            if (isset($attribute) && strlen($attribute) > 0)
                $htmlAttributes[] = sprintf('%s="%s"', $key, addcslashes($attribute, '"'));

        return sprintf('<input %s />', implode(' ', $htmlAttributes));
    }

    protected static function getLabel($field) {
        $pieces = preg_split('/(?=[A-Z])/', $field);
        array_map('strtolower', $pieces);
        $pieces[0] = ucfirst($pieces[0]);

        return implode(' ', $pieces);
    }
}