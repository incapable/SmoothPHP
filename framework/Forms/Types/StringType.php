<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * StringType.php
 * Type for html's input[type="text"]
 */

namespace SmoothPHP\Framework\Forms\Types;

use SmoothPHP\Framework\Forms\Containers\Type;

class StringType extends Type {

    public function __construct($field, array $attributes) {
        parent::__construct($field, $attributes);
        $this->attributes = array_replace_recursive(array(
            'attr' => array(
                'type' => 'text',
                'placeholder' => '...'
            )
        ), $this->attributes);
    }

}