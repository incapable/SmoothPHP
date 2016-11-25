<?php

namespace SmoothPHP\Framework\Forms;

use SmoothPHP\Framework\Flow\Requests\Request;

interface Constraint {

    public function checkConstraint(Request $request, $name, $value, array &$failReasons);

}