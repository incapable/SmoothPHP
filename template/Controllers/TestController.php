<?php

namespace Test\Controllers;

use SmoothPHP\Framework\Flow\Responses\TemplateResponse;

class TestController {
    public $testVar = 'test value';

    public function index() {
        return new TemplateResponse('test.tpl', array(
            'var' => 255,
            'ctrl' => $this
        ));
    }

    public function test() {
        return 'test method called!';
    }

    public function getSelf() {
        return $this;
    }

    public function testInt() {
        return 10;
    }

}