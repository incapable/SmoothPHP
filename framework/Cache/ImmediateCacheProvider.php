<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * ImmediateCacheProvider.php
 * Description
 */

namespace SmoothPHP\Framework\Cache;

class ImmediateCacheProvider extends RuntimeCacheProvider {

    public function fetch($sourceFile, callable $cacheBuilder = null) {
        $cacheBuilder = $cacheBuilder ?: $this->cacheBuilder;
        return $cacheBuilder($sourceFile);
    }

}