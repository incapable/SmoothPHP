<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * HiddenType.php
 * Type for html's input[type="hidden"]
 */


namespace SmoothPHP\Framework\Forms\Types;

use SmoothPHP\Framework\Forms\Containers\Type;

class HiddenType extends Type {

    public function __construct($field) {
        parent::__construct($field);
        $this->attributes = array_replace_recursive($this->attributes, array(
            'attr' => array(
                'type' => 'hidden',
            )
        ));
    }

    public function getContainer() {
        return array(
            'rowstart' => '',
            'label' => '',
            'rowseparator' => '',
            'input' => $this,
            'rowend' => ''
        );
    }

}