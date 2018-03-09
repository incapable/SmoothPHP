<?php

/**
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * **********
 * Copyright © 2015-2018
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * **********
 * Utilities.php
 */

/**
 * Convenience method that doesn't use an array-reference as argument.
 * @param mixed $array The array (or object to be casted to array) to get the last element of.
 * @return mixed The last element of the passed array.
 * @see end()
 */
function last($array) {
	$real = (array)$array;
	return end($real);
}

/**
 * Method that wraps md5_file with a simple caching mechanism to prevent calculating the same file multiple times
 * @param $filename string Path to the file
 * @return string|null md5 checksum or null if the file doesn't exist
 * @note The results and file existence are cached, consecutive calls to this function will return even if the file no longer exists.
 */
function file_hash($filename) {
	$filename = realpath($filename);

	if (!file_exists($filename))
		return null;

	if (__ENV__ == 'prod')
		return md5(filemtime($filename));
	else
		return md5_file($filename);
}

function cookie_domain() {
	global $request;
	$domain = explode('.', $request->server->SERVER_NAME);
	if (count($domain) < 2)
		$cookieDomain = $request->server->SERVER_NAME;
	else
		$cookieDomain = sprintf('.%s.%s', $domain[count($domain) - 2], $domain[count($domain) - 1]);
	return $cookieDomain;
}

/*
 * If we don't have PHP7's random_bytes, fall back to openssl_random_pseudo_bytes
 */
if (!function_exists('random_bytes')) {
	function random_bytes($length) {
		return openssl_random_pseudo_bytes($length);
	}
}