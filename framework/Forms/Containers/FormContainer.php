<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * FormContainer.php
 * Description
 */

namespace SmoothPHP\Framework\Forms\Containers;

class FormContainer {
    private $backing;

    public function __construct(array $backing) {
        $this->backing = $backing;
    }

    public function __get($name) {
        return $this->backing[$name];
    }

    public function __toString() {
        $result = '';
        foreach($this->backing as $element)
            $result .= $element;
        return $result;
    }
}