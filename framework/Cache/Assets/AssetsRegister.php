<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * AssetsRegister.php
 * Description
 */

namespace SmoothPHP\Framework\Cache\Assets;

class AssetsRegister {
    private $js, $css;

    public function __construct() {
        $this->js = array();
        $this->css = array();
    }
}