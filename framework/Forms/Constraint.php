<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Constraint.php
 * Interface for all form input constraints.
 */

namespace SmoothPHP\Framework\Forms;

use SmoothPHP\Framework\Flow\Requests\Request;

abstract class Constraint {

    public function setAttributes(array &$attributes) {}

    public abstract function checkConstraint(Request $request, $name, $label, $value, array &$failReasons);

}