<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * FileStream.php
 * Response that will yield a file to the browser.
 */

namespace SmoothPHP\Framework\Flow\Responses;

use SmoothPHP\Framework\Core\Kernel;
use SmoothPHP\Framework\Flow\Requests\Request;

class FileStream extends Response {

	const CACHE_DATE = 'D, d M Y H:i:s \G\M\T';

	private $options;
	private $request;

	public function build(Kernel $kernel, Request $request) {
		$options = is_array($this->controllerResponse) ? $this->controllerResponse : ['url' => $this->controllerResponse];
		$this->request = $request;
		$urlParts = explode('/', $options['url']);
		$this->options = array_merge([
			'type'     => 'application/octet-stream',
			'filename' => end($urlParts),
			'expires'  => 86400,
			'cache'    => false,
			'cors'     => true
		], $options);

		if (!file_exists($this->options['url']))
			throw new \RuntimeException("File does not exist!");
	}

	protected function sendHeaders() {
		parent::sendHeaders();

		header('Content-Type: ' . $this->options['type']);
		header('Content-Disposition: ' . (strpos($this->options['type'], 'text/') === 0 ? 'inline' : 'attachment') . '; filename="' . $this->options['filename'] . '"');
		header('Content-Length: ' . filesize($this->options['url']));
		if ($this->options['cors'])
			header('Access-Control-Allow-Origin: *');

		if ($this->options['cache']) {
			$eTag = cached_md5_file($this->options['url']);
			$lastModified = filemtime($this->options['url']);

			if (__ENV__ != 'dev') {
				header('Cache-Control: max-age=' . $this->options['expires'] . ', private');
				header('Expires: ' . gmdate(self::CACHE_DATE, time() + $this->options['expires']));
				header('Pragma: private');
			} else {
				header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
				header('Expires: ' . gmdate(self::CACHE_DATE, 0));
			}
			header('Last-Modified: ' . gmdate(self::CACHE_DATE, $lastModified));
			header('ETag: ' . $eTag);

			if ($this->request->server->HTTP_IF_MODIFIED_SINCE && $lastModified > strtotime($this->request->server->HTTP_IF_MODIFIED_SINCE)) {
				http_response_code(304);
				exit();
			}
			if ($this->request->server->HTTP_IF_NONE_MATCH && $this->request->server->HTTP_IF_NONE_MATCH == $eTag) {
				http_response_code(304);
				exit();
			}
		}
	}

	protected function sendBody() {
		readfile($this->options['url']);
	}

}
