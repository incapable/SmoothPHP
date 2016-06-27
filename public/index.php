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

require_once '../framework/Bootstrap.php';
use SmoothPHP\Framework\Core\Kernel;
use SmoothPHP\Framework\Flow\Requests\Request;

$kernel = new Kernel();
$kernel->loadPrototype(new Website());

$request = Request::createFromGlobals();
$response = $kernel->getResponse($request);
$response->send();