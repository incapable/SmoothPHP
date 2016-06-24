<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * PasswordType.php
 * Description
 */

namespace SmoothPHP\Framework\Forms\Types;

class PasswordType extends StringType{

    public function __construct($field, array $attributes) {
        parent::__construct($field, $attributes);
        $this->attributes = array_replace_recursive($this->attributes, array(
            'attr' => array(
                'type' => 'password'
            )
        ));
    }

}