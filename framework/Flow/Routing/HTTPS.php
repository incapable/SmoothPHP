<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * SSL.php
 * Enum for different SSL modes in routing
 */

namespace SmoothPHP\Framework\Flow\Routing;

class HTTPS {
	const IGNORE = 0;
	const ENFORCE_INACTIVE = 1;
	const ENFORCE_ACTIVE = 2;
}