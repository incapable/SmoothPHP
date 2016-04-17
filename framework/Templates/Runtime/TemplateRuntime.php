<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * TemplateRuntime.php
 * Description
 */

namespace SmoothPHP\Framework\Templates\Runtime;

abstract class TemplateRuntime {

    abstract function verify_cache($file, $md5);

    abstract function call_function();

    abstract function set_var($key, $value);

    abstract function get_var($key);

}