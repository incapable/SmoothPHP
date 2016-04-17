<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * PHPBuilder.php
 * Description
 */

namespace SmoothPHP\Framework\Templates\Compiler;


class PHPBuilder {
    private $content;

    private $isTagOpen;

    public function __construct() {
        $this->content = '';
        $this->isTagOpen = true; // eval() starts with PHP-tags opened
    }

    public function openPHP() {
        if (!$this->isTagOpen) {
            $this->isTagOpen = true;
            $this->content .= '<?php ';
        }
    }

    public function closePHP() {
        if ($this->isTagOpen) {
            $this->isTagOpen = false;
            $this->content .= ' ?>';
        }
    }

    public function isTagOpen() {
        return $this->isTagOpen;
    }

    public function append($code) {
        $this->content .= $code;
    }

    public function getPHP() {
        return $this->content;
    }

}