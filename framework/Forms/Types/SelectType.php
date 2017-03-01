<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * SelectType.php
 * Type for html's select
 */

namespace SmoothPHP\Framework\Forms\Types;

use SmoothPHP\Framework\Flow\Requests\Request;
use SmoothPHP\Framework\Forms\Containers\Type;

class SelectType extends Type {

    const KEY_SELECTOR = 0x1;
    const VALUE_SELECTOR = 0x2;

    const KEY_ONLY = (self::KEY_SELECTOR << 4) | self::KEY_SELECTOR;
    const VALUE_ONLY = (self::VALUE_SELECTOR << 4) | self::VALUE_SELECTOR;
    const KEY_VALUE_PAIR = (self::KEY_SELECTOR << 4) | self::VALUE_SELECTOR;
    const KEY_VALUE_INVERSE = (self::VALUE_SELECTOR << 4) | self::KEY_SELECTOR;

    public function __construct($field) {
        parent::__construct($field);
        $this->attributes = array_replace_recursive($this->attributes, array(
            'options_mode' => self::KEY_VALUE_PAIR,
            'strict' => true,
            'options' => array(),
            'options_attr' => array()
        ));
    }

    public function checkConstraint(Request $request, $name, $value, array &$failReasons) {
        parent::checkConstraint($request, $name, $value, $failReasons);

        if ($this->attributes['strict']) {
            $mode = last($this->attributes['options_mode']);
            $method = (((($mode >> 4) & self::KEY_SELECTOR) == self::KEY_SELECTOR) ? 'array_keys' : 'array_values');
            $options = call_user_func($method, $this->attributes['options']);

            if (!in_array($value, $options)) {
                global $kernel;
                $failReasons[] = sprintf($kernel->getLanguageRepository()->getEntry('smooth_form_selectvalue'), $value, $name);
            }
        }
    }

    public function __toString() {
        try {
            $attributes = $this->attributes['attr'];

            $attributes['id'] = $this->field;
            $attributes['name'] = $this->field;

            $mode = last($this->attributes['options_mode']);
            $options = array();
            $optionsAttr = $this->transformAttributes($this->attributes['options_attr']);

            foreach ($this->attributes['options'] as $key => $value) {
                $optionValue = ((($mode >> 4) & self::KEY_SELECTOR) == self::KEY_SELECTOR) ? $key : $value;
                $labelValue = (($mode & self::KEY_SELECTOR) == self::KEY_SELECTOR) ? $key : $value;
                $options[] = sprintf('<option value="%s" %s>%s</option>', $optionValue, $optionsAttr, $labelValue);
            }

            return sprintf('<select %s>%s</select>', $this->transformAttributes($attributes), implode(' ', $options));
        } catch (\Exception $e) {
            return '';
        }
    }

}