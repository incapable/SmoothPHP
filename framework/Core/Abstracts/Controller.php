<?php

namespace SmoothPHP\Framework\Core\Abstracts;

use SmoothPHP\Framework\Flow\Responses\TemplateResponse;

abstract class Controller {

    public function onInitialize() {}

    protected static function render($template, array $templateArgs = array()) {
        return new TemplateResponse($template, $templateArgs);
    }

}