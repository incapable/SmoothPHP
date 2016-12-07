<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * BasicClassLoader.php
 * "Basic" class loader relying on PHP's require_once to load PHP classes without any special features.
 */

namespace SmoothPHP\Framework\Core\ClassLoader;

class BasicClassLoader {
    private $prefixes;

    public function __construct() {
        $this->prefixes = array();
        $this->prefixes['SmoothPHP\Framework'] = __ROOT__ . 'framework/';
        $this->prefixes[''] = __ROOT__ . 'src/';
    }

    public function register() {
        spl_autoload_register(array($this, 'loadClass'), true, false);
    }

    public function loadClass($class) {
        if ($file = $this->findClassFile($class))
            require_once $file;

        return $file;
    }

    protected function findClassFile($class) {
        if ($class[0] == '\\')
            $class = substr($class, 1);

        if (($pos = strrpos($class, '\\')) !== false) {
            $classPath = str_replace('\\', '/', substr($class, 0, $pos)) . '/';
            $className = substr($class, $pos + 1);
        } else {
            $classPath = null;
            $className = $class;
        }

        $classPath .= str_replace('_', '/', $className) . '.php';

        foreach ($this->prefixes as $prefix => $dir) {
            if (empty($prefix) || strpos($class, $prefix) === 0) {
                if (!empty($prefix))
                    $prefix = $prefix . '\\';
                $prefix = str_replace('\\', '/', $prefix);
                $ldPath = preg_replace('#' . $prefix . '#', $dir, $classPath, 1);
                if (($realpath = realpath($ldPath)) === false) {
                    clearstatcache(true, $ldPath);
                    $realpath = realpath($ldPath);
                }

                if (file_exists($realpath))
                    return $realpath;
            }
        }

        if ($file = stream_resolve_include_path($classPath))
            return $file;

        // Regular means don't work
        $next = strstr($class, '\\');
        if ($next)
            return $this->findClassFile($next);
    }

}