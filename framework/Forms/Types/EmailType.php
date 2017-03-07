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

    public function __construct($field) {
        parent::__construct($field);

        global $kernel;
        $this->attributes = array_replace_recursive($this->attributes, array(
            'attr' => array(
                'type' => 'email',
                'placeholder' => $kernel->getLanguageRepository()->getEntry('smooth_form_email')
            )
        ));
    }

    public function checkConstraint(Request $request, $name, $label, $value, array &$failReasons) {
        parent::checkConstraint($request, $name, $label, $value, $failReasons);

        if (!$request->post->email->get($this->field)) {
            global $kernel;
            $failReasons[] = sprintf($kernel->getLanguageRepository()->getEntry('smooth_form_email_invalid'), $value);
        }
    }

}