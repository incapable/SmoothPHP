<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * CacheProvider.php
 * Description
 */

namespace SmoothPHP\Framework\Cache;

abstract class CacheProvider {

    abstract function fetch($sourceFile, callable $cacheBuilder = null);

}