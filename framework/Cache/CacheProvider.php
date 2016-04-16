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

use SmoothPHP\Framework\Core\Lock;

class CacheProvider {
    const PERMS = 0755;

    private $cacheFileFormat;
    private $cacheBuilder;
    private $readCache, $writeCache;

    public function __construct($folder, $ext = null, callable $cacheBuilder, callable $readCache = null, callable $writeCache = null) {
        $ext = $ext ?: $folder;
        $this->cacheFileFormat = sprintf('%scache/%s/%s.%s.%s', __ROOT__, $folder, '%s', '%s', $ext);

        $this->cacheBuilder = $cacheBuilder ?: 'file_get_contents';
        $this->readCache = $readCache ?: 'file_get_contents';
        $this->writeCache = $writeCache ?: 'file_put_contents';
    }

    public function fetch($sourceFile) {
        $fileName = str_replace(array('/', '\\'), array('_', '_'), str_replace(__ROOT__, '', $sourceFile));
        $checksum = md5_file($sourceFile);

        $cacheFile = sprintf($this->cacheFileFormat, $fileName, $checksum);
        if (!is_dir(dirname($cacheFile)))
            mkdir(dirname($cacheFile), self::PERMS, true);

        if (file_exists(sprintf($cacheFile)))
            return call_user_func($this->readCache, $cacheFile);
        else {
            $lock = new Lock($fileName);

            if ($lock->lock()) {
                array_map('unlink', glob(sprintf($this->cacheFileFormat, $fileName, '*')));
                $newCache = call_user_func($this->cacheBuilder, $sourceFile);
                call_user_func($this->writeCache, $cacheFile, $newCache);
                return $newCache;
            } else
                return call_user_func($this->readCache, $cacheFile);
        }
    }
}
