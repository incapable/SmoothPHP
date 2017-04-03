<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * FileType.php
 * Type for html's input[type="file"]
 */

namespace SmoothPHP\Framework\Forms\Types;

use SmoothPHP\Framework\Flow\Requests\Request;
use SmoothPHP\Framework\Forms\Containers\Type;

class FileType extends Type {

    public function __construct($field) {
        parent::__construct($field);
        $this->attributes = array_replace_recursive($this->attributes, array(
            'attr' => array(
                'type' => 'file'
            ),
            'required' => false
        ));
    }
    public function checkConstraint(Request $request, $name, $label, $value, array &$failReasons) {
        global $kernel;
        $language = $kernel->getLanguageRepository();

        if ((!$request->files->{$name} || $request->files->{$name}->error == UPLOAD_ERR_NO_FILE)) {
            if (last($this->attributes['required'])) {
                $failReasons[] = sprintf($kernel->getLanguageRepository()->getEntry('smooth_form_file_required'), $label);
            }

            return;
        }

        parent::checkConstraint($request, $name, $label, $value, $failReasons);

        switch($request->files->{$name}->error) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_FORM_SIZE:
            case UPLOAD_ERR_INI_SIZE:
                $failReasons[] = sprintf($language->getEntry('smooth_form_file_size'), $label);
                break;
            default:
                $failReasons[] = sprintf($language->getEntry('smooth_form_file_genericerror'), $label);
        }
    }


}