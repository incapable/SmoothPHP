<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * GlobalVarRequest.php
 * A request implementation that will use server vars such as $_GET, $_POST and $_SERVER to provide data.
 */

namespace SmoothPHP\Framework\Flow\Request;

class GlobalVarRequest extends Request {
    
    public function __construct() {
        parent::__construct($_GET, $_POST, $_SERVER);
    }
    
}