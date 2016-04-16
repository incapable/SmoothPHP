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

    private $phpTagOpen;

    public function __construct() {
        $this->content = '';
        $this->phpTagOpen = false;
    }

    public function openPHP() {
        if (!$this->phpTagOpen) {
            $this->phpTagOpen = true;
            $this->content .= '<?php ';
        }
    }

    public function closePHP() {
        if ($this->phpTagOpen) {
            $this->phpTagOpen = false;
            $this->content .= ' ?>';
        }
    }

    public function isPHPTagOpen() {
        return $this->phpTagOpen;
    }

    public function append($code) {
        $this->content .= $code;
    }

    public function getPHP() {
        return $this->content;
    }

}