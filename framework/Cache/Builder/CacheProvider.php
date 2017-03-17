<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * CacheProvider.php
 * Abstract cache provider
 */

namespace SmoothPHP\Framework\Cache\Builder;

abstract class CacheProvider {

    abstract function fetch($sourceFile, $cacheBuilder = null, $readCache = null, $writeCache = null);

}