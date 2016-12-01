<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * FileDataSource.php
 * Simple file source for localization strings.
 */

namespace SmoothPHP\Framework\Localization;

use SmoothPHP\Framework\Cache\Builder\RuntimeCacheProvider;

class FileDataSource implements DataSource {
    private static $cache;
    private $folder;

    public function __construct($folder) {
        if (!isset(self::$cache))
            self::$cache = RuntimeCacheProvider::create(function($folder) {
                $dir = opendir($folder);
                $entries = array();
                while(($file = readdir($dir)) !== false) {
                    if ($file == '.' || $file == '..')
                        continue;
                    $entries = array_merge_recursive(parse_ini_file($folder . DIRECTORY_SEPARATOR . $file, true, INI_SCANNER_RAW), $entries);
                }
                return $entries;
            });
        $this->folder = $folder;
    }

    public function getEntry($language, $key) {
        $entries = self::$cache->fetch($this->folder);
        if (!isset($entries[$language][$key]))
            return null;
        return $entries[$language][$key];
    }

}