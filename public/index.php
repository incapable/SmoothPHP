<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * index.php
 * Primary entry point for SmoothPHP.
 */

define('__ENV__', 'dev'); // Options: dev, debug, production

use SmoothPHP\Framework\Flow\Requests\Request;

{
    $loader = require_once '../framework/Bootstrap.php';
    $loader(new Website());
}

$request = Request::createFromGlobals();
$response = $kernel->getResponse($request);
$response->send();