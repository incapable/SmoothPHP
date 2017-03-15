<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Utilities.php
 * PHP file containing several convenience methods, loaded during Bootstrap
 */

/**
 * Convenience method that doesn't use an array-reference as argument.
 * @param mixed $array The array (or object to be casted to array) to get the last element of.
 * @return mixed The last element of the passed array.
 * @see end()
 */
function last($array) {
    $real = (array) $array;
    return end($real);
}

/**
 * Method that wraps md5_file with a simple caching mechanism to prevent calculating the same file multiple times
 * @param $filename string Path to the file
 * @return string m5 checksum
 * @note The results and file existence are cached, consecutive calls to this function will return even if the file no longer exists.
 */
function cached_md5_file($filename) {
    static $md5Cache = array();
    $filename = realpath($filename);

    if (!isset($md5Cache[$filename]))
        $md5Cache[$filename] = $result = md5_file($filename);
    else
        $result = $md5Cache[$filename];

    return $result;
}