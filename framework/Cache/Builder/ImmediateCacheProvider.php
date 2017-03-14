<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * ImmediateCacheProvider.php
 * CacheProvider that serves as a fallback option in case APC is not available.
 */

namespace SmoothPHP\Framework\Cache\Builder;

class ImmediateCacheProvider extends RuntimeCacheProvider {

    public function fetch($sourceFile, callable $cacheBuilder = null, callable $readCache = null, callable $writeCache = null) {
        $cacheBuilder = $cacheBuilder ?: $this->cacheBuilder;
        return $cacheBuilder($sourceFile);
    }

}