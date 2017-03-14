<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * RequiredConstraint.php
 * Constraint for forms that requires this particular input field to be filled.
 */

namespace SmoothPHP\Framework\Forms\Constraints;

use SmoothPHP\Framework\Forms\Constraint;
use SmoothPHP\Framework\Flow\Requests\Request;

class RequiredConstraint extends Constraint {

    public function setAttributes(array &$attributes) {
        $attributes['attr']['required'] = 'required';
    }

    public function checkConstraint(Request $request, $name, $label, $value, array &$failReasons) {
        if (!isset($value) || strlen($value) == 0) {
            global $kernel;
            $failReasons[] = sprintf($kernel->getLanguageRepository()->getEntry('smooth_form_required'), $label);
        }
    }

}