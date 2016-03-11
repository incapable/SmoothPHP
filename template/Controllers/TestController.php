<?php

namespace Test\Controllers;

use SmoothPHP\Framework\Core\Kernel;
use SmoothPHP\Framework\Flow\Requests\Request;

class TestController {
    
    public function index(Kernel $kernel, $arg0, Request $request, $arg1) {
        var_dump($kernel);
        var_dump($request);
        var_dump($arg0);
        var_dump($arg1);
    }
    
}