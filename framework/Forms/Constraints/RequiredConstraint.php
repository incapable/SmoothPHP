<?php

namespace SmoothPHP\Framework\Forms\Constraints;

use SmoothPHP\Framework\Forms\Constraint;
use SmoothPHP\Framework\Flow\Requests\Request;

class RequiredConstraint implements Constraint {

    public function checkConstraint(Request $request, $name, $value, array &$failReasons) {
        if (!isset($value) || empty($value))
            $failReasons[] = $name . ' can not be empty!';
    }

}