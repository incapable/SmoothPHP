<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * APCCacheProvider.php
 * RAM-cache provider, capable of storing values that are often used in RAM
 */

namespace SmoothPHP\Framework\Cache\Builder;

use SmoothPHP\Framework\Core\Lock;

class APCCacheProvider extends RuntimeCacheProvider {
    private static $apcKey;

    private $cacheFetch, $cacheStore;

    public function __construct(callable $cacheBuilder, callable $cacheFetch, $cacheStore) {
        $this->cacheFetch = $cacheFetch;
        $this->cacheStore = $cacheStore;

        if (!isset(self::$apcKey)) {
            self::$apcKey = (new FileCacheProvider("conf", "txt", function () {
                return md5(mt_rand(0, 99999));
            }))->fetch('apc_app_id');
        }

        parent::__construct($cacheBuilder);
    }

    public function fetch($sourceFile, callable $cacheBuilder = null) {
        $cacheBuilder = $cacheBuilder ?: $this->cacheBuilder;
        $realPath = realpath($sourceFile);
        $checksum = md5_file($realPath);

        $cacheItem = call_user_func($this->cacheFetch, self::$apcKey . $realPath);

        if (isset($cacheItem) && $cacheItem->md5 == $checksum)
            return $cacheItem->value;
        else {
            $lock = new Lock(md5($realPath));

            if ($lock->lock()) {
                $newCache = call_user_func($cacheBuilder, $realPath);
                call_user_func($this->cacheStore, self::$apcKey . $realPath);
                return $newCache;
            } else
                return call_user_func($this->cacheFetch, self::$apcKey . $realPath);
        }
    }

}

class APCCacheItem {
    public $md5;
    public $value;
}