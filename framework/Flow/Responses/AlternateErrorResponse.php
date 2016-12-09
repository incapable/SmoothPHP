<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * AlternateErrorResponse.php
 * Interface indicating a response requires a different method of handling errors.
 */

namespace SmoothPHP\Framework\Flow\Responses;

interface AlternateErrorResponse {

    public function buildErrorResponse($message);

}