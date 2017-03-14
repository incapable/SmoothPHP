<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * MinimumLengthConstraint.php
 * Constraint for forms that requires this particular field to have a minimum length.
 */

namespace SmoothPHP\Framework\Forms\Constraints;

use SmoothPHP\Framework\Forms\Constraint;
use SmoothPHP\Framework\Flow\Requests\Request;

class MinimumLengthConstraint extends Constraint {
    private $minLength;

    public function __construct($minLength = 0) {
        $this->minLength = $minLength;
    }

    public function setAttributes(array &$attributes) {
        $attributes['attr']['minlength'] = $this->minLength;
    }

    public function checkConstraint(Request $request, $name, $label, $value, array &$failReasons) {
        if (strlen($value) < $this->minLength) {
            global $kernel;
            $failReasons[] = sprintf($kernel->getLanguageRepository()->getEntry('smooth_form_minlength'), $label, $this->minLength);
        }
    }

}