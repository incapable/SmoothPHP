<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
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