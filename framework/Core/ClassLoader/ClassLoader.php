<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * ClassLoader.php
 * Class loader interface
 */

namespace SmoothPHP\Framework\Core\ClassLoader;

interface ClassLoader {

	public function addPrefix($namespace, $path);

	public function register();

}