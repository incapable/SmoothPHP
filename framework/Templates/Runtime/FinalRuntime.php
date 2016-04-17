<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * FinalRuntime.php
 * Description
 */

namespace SmoothPHP\Framework\Templates\Runtime;

use SmoothPHP\Framework\Cache\CacheExpiredException;

class FinalRuntime extends TemplateRuntime {
    private $vars;

    public function __construct(array $args) {
        $this->vars = $args;
    }

    public function verify_cache($file, $md5) {
        if (md5_file($file) != $md5)
            throw new CacheExpiredException();
    }

    public function call_function() {
        $args = func_get_args();
        call_user_func_array($args[0], array_slice($args, 1));
    }

    public function set_var($key, $value) {
        $this->vars[$key] = $value;
    }

    public function get_var($key) {
        return $this->vars[$key];
    }

}