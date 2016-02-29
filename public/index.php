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
use SmoothPHP\Framework\Flow\GlobalVarRequest;

{
    $kernel = new Kernel();
    $request = new GlobalVarRequest();
}