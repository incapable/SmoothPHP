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

interface Constraint {

    public function checkConstraint(Request $request, $name, $value, array &$failReasons);

}