<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * SubmitType.php
 * Type for html's input[type="submit"]
 */

namespace SmoothPHP\Framework\Forms\Types;

use SmoothPHP\Framework\Forms\Containers\Type;

class SubmitType extends Type {

    public function __construct($field) {
        parent::__construct($field);
        $this->attributes = array_replace_recursive($this->attributes, array(
            'attr' => array(
                'type' => 'submit',
                'value' => $this->attributes['label']
            )
        ));
    }

    public function generateLabel() {
        return '';
    }

}