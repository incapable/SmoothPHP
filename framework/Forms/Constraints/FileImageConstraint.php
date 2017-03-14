<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * FileImageConstraint.php
 * Constraint for uploads that must be an image.
 */

namespace SmoothPHP\Framework\Forms\Constraints;

use SmoothPHP\Framework\Forms\Constraint;
use SmoothPHP\Framework\Flow\Requests\Request;

class FileImageConstraint extends Constraint {
    private $acceptMime;

    public function __construct($acceptMime = 'image/*') {
        $this->acceptMime = $acceptMime;
    }

    public function setAttributes(array &$attributes) {
        $attributes['attr']['accept'] = $this->acceptMime;
    }

    public function checkConstraint(Request $request, $name, $label, $value, array &$failReasons) {
        if (strpos($this->acceptMime, 'image/') !== false) {
            $imageType = exif_imagetype($request->files->{$name}->location);
            if ($imageType === false || !fnmatch($this->acceptMime, image_type_to_mime_type($imageType))) {
                global $kernel;
                $failReasons[] = sprintf($kernel->getLanguageRepository()->getEntry('smooth_form_file_image_invalid'), $label);
            }
        }
    }

}