<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * FileImageSizeConstraint.php
 * Constraint for file image uploads that require certain image dimensions.
 */

namespace SmoothPHP\Framework\Forms\Constraints;

use SmoothPHP\Framework\Forms\Constraint;
use SmoothPHP\Framework\Flow\Requests\Request;

class FileImageSizeConstraint extends Constraint {
    private $min, $max;

    public function __construct($max, $min = array(0, 0)) {
        $this->min = $min;
        $this->max = $max;
    }

    public function setAttributes(array &$attributes) {
    }

    public function checkConstraint(Request $request, $name, $label, $value, array &$failReasons) {
        $imageSize = getimagesize($request->files->{$name}->location);
        if ($imageSize === false) {
            global $kernel;
            $failReasons[] = sprintf($kernel->getLanguageRepository()->getEntry('smooth_form_file_image_invalid'), $label);
            return;
        }

        if ($imageSize[0] < $this->min[0] || $imageSize[1] < $this->min[1]) {
            global $kernel;
            $failReasons[] = sprintf($kernel->getLanguageRepository()->getEntry('smooth_form_file_image_toosmall'), $label, $this->min[0], $this->min[1]);
            return;
        }

        if ($imageSize[0] > $this->max[0] || $imageSize[1] > $this->max[1]) {
            global $kernel;
            $failReasons[] = sprintf($kernel->getLanguageRepository()->getEntry('smooth_form_file_image_toolarge'), $label, $this->max[0], $this->max[1]);
        }
    }

}