<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * CacheProvider.php
 * Abstract cache provider
 */

namespace SmoothPHP\Framework\Cache\Builder;

abstract class CacheProvider {

    abstract function fetch($sourceFile, callable $cacheBuilder = null, callable $readCache = null, callable $writeCache = null);

}