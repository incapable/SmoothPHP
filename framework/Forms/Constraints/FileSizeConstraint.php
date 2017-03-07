<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * FileSizeConstraint.php
 * Constraint for forms that require a file size limitation.
 */

namespace SmoothPHP\Framework\Forms\Constraints;

use SmoothPHP\Framework\Forms\Constraint;
use SmoothPHP\Framework\Flow\Requests\Request;

class FileSizeConstraint extends Constraint {
    private $fileSize;

    public function __construct($fileSize) {
        $this->fileSize = $fileSize;
    }

    public function setAttributes(array &$attributes) {
        // TODO Add javascript file-size checking
    }

    public function checkConstraint(Request $request, $name, $label, $value, array &$failReasons) {
        if ($request->files->{$name}->size > $this->fileSize) {
            global $kernel;
            $failReasons[] = sprintf($kernel->getLanguageRepository()->getEntry('smooth_form_file_size'), $label);
        }
    }

}