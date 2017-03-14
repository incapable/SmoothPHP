<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * FileElement.php
 * The object returned from FileSource that allows interaction with uploaded files.
 */

namespace SmoothPHP\Framework\Flow\Requests\Files;

class FileElement {
    private $file;

    public function __construct(array $file) {
        $this->file = $file;
    }

    public function __get($name) {
        switch($name) {
            case 'location':
                $name = 'tmp_name';
            case 'name':
            case 'tmp_name':
            case 'error':
            case 'size':
                return $this->file[$name];
        }
    }

    public function saveAs($path) {
        if (!move_uploaded_file($this->file['tmp_name'], $path))
            throw new \RuntimeException('Could not move uploaded file.');
    }

}