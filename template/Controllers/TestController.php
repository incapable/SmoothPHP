<?php

namespace Test\Controllers;

use SmoothPHP\Framework\Core\Kernel;

class TestController {
    
    public function index(Kernel $rq, $arg0, $arg1) {
        var_dump($rq);
        var_dump($arg0);
        var_dump($arg1);
    }
    
}