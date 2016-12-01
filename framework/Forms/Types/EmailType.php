<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * EmailType.php
 * Type for html's input[type="email"]
 */

namespace SmoothPHP\Framework\Forms\Types;

use SmoothPHP\Framework\Flow\Requests\Request;
use SmoothPHP\Framework\Forms\Containers\Type;

class EmailType extends Type {

    public function __construct($field, array $attributes) {
        parent::__construct($field, $attributes);

        global $kernel;
        $this->attributes = array_replace_recursive(array(
            'attr' => array(
                'type' => 'email',
                'placeholder' => $kernel->getLanguageRepository()->getEntry('smooth_form_email')
            )
        ), $this->attributes);
    }

    public function checkConstraint(Request $request, $name, $value, array &$failReasons) {
        parent::checkConstraint($request, $name, $value, $failReasons);

        if (!$request->post->email->get($this->field)) {
            global $kernel;
            $failReasons[] = sprintf($kernel->getLanguageRepository()->getEntry('smooth_form_email_invalid'), $value);
        }
    }

}