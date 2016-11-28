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

class RequiredConstraint implements Constraint {

    public function checkConstraint(Request $request, $name, $value, array &$failReasons) {
        if (!isset($value) || empty($value))
            $failReasons[] = $name . ' can not be empty!';
    }

}