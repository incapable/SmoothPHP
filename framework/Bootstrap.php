<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Bootstrap.php
 * This file is responsible for initializing the classloader so that the index file may create a website kernel.
 */

use SmoothPHP\Framework\Core\ClassLoader\BasicClassLoader;

// Set an error handler that uses exceptions instead
set_error_handler(function ($num, $str, $file, $line) {
    throw new ErrorException($str, 0, $num, $file, $line);
});

{
    if (!defined('__ROOT__'))
        define('__ROOT__', str_replace('public', '', $_SERVER['DOCUMENT_ROOT']));

    require_once __ROOT__ . '/framework/Core/ClassLoader/BasicClassLoader.php';

    $classLoader = new BasicClassLoader();
    $classLoader->register();
}