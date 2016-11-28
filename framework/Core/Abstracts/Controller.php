<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Controller.php
 * Abstract controller style, doesn't contain actual implementation.
 */

namespace SmoothPHP\Framework\Core\Abstracts;

use SmoothPHP\Framework\Flow\Responses\TemplateResponse;

abstract class Controller {

    public function onInitialize() {}

    protected static function render($template, array $templateArgs = array()) {
        return new TemplateResponse($template, $templateArgs);
    }

}