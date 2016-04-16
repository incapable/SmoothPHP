<?php

namespace Test\Controllers;

use SmoothPHP\Framework\Flow\Responses\TemplateResponse;

class TestController {

    public function index() {
        return new TemplateResponse('test');
    }

}