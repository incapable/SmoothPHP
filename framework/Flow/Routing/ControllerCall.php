<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * ControllerCall.php
 * A parsed controller call, that is responsible for passing the right arguments
 */

namespace SmoothPHP\Framework\Flow\Routing;

class ControllerCall {

    public function __construct($controller, $call) {
        $method = new \ReflectionMethod($controller, $call);
        var_dump($method->getParameters());
    }

}