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

	/* @var Request */
	protected $request;

	public function build(Kernel $kernel, Request $request) {
		$options = is_array($this->controllerResponse) ? $this->controllerResponse : ['url' => $this->controllerResponse];
		$this->request = $request;
		$urlParts = explode('/', $options['url']);
		$this->controllerResponse = array_merge([
			'type'     => 'application/octet-stream',
			'filename' => end($urlParts),
			'expires'  => 86400,
			'cache'    => false,
			'cors'     => true
		], $options);

		// Check if a local file exists
		if (!file_exists($this->controllerResponse['url'])) {
			// No? Let's check if the file starts with HTTP instead
			if (strtolower(substr($this->controllerResponse['url'], 0, 4)) == 'http') {
				// It does, get the headers to verify if it exists and get some useful headers
				$headers = get_headers($this->controllerResponse['url']);
				$response = (int)substr($headers[0], 9, 3);

				// Success check
				if ($response >= 200 && $response < 300) {
					// Okay, the resource exists, get the content length for later usage

					foreach ($headers as $header) {
						if (strpos(strtoupper($header), 'HTTP/') !== false)
							continue;

						list($key, $value) = explode(': ', $header);
						switch (strtolower($key)) {
							case 'content-length':
								$this->controllerResponse['size'] = (int)$value;
						}
					}

					// Return without throwing
					return;
				}
			}
			throw new \RuntimeException("File does not exist!");
		}
	}

	protected function sendHeaders() {
		parent::sendHeaders();

		header('Content-Type: ' . $this->controllerResponse['type']);
		header('Content-Disposition: ' . (
			strpos($this->controllerResponse['type'], 'text/') === 0
			|| strpos($this->controllerResponse['type'], 'image/') === 0
				? 'inline' : 'attachment') . '; filename="' . $this->controllerResponse['filename'] . '"');
		header('Content-Length: ' . (isset($this->controllerResponse['size']) ? $this->controllerResponse['size'] : filesize($this->controllerResponse['url'])));
		if ($this->controllerResponse['cors'])
			header('Access-Control-Allow-Origin: *');

		if ($this->controllerResponse['cache']) {
			if (__ENV__ != 'dev') {
				header('Cache-Control: max-age=' . $this->controllerResponse['expires'] . ', private');
				header('Expires: ' . gmdate(self::CACHE_DATE, time() + $this->controllerResponse['expires']));
				header('Pragma: private');
			} else {
				header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
				header('Expires: ' . gmdate(self::CACHE_DATE, 0));
			}

			if (isset($this->controllerResponse['url'])) {
				$eTag = file_hash($this->controllerResponse['url']);
				$lastModified = filemtime($this->controllerResponse['url']);

				header('Last-Modified: ' . gmdate(self::CACHE_DATE, $lastModified));
				header('ETag: W/"' . $eTag . '"');

				if ($this->request->server->HTTP_IF_MODIFIED_SINCE && $lastModified > strtotime($this->request->server->HTTP_IF_MODIFIED_SINCE)) {
					http_response_code(304);
					exit();
				}
				if ($this->request->server->HTTP_IF_NONE_MATCH && $this->request->server->HTTP_IF_NONE_MATCH == $eTag) {
					http_response_code(304);
					exit();
				}
			}

			if (isset($this->controllerResponse['data'])) {
				$eTag = md5($this->controllerResponse['data']);
				header('ETag: "' . $eTag . '"');

				if ($this->request->server->HTTP_IF_NONE_MATCH && $this->request->server->HTTP_IF_NONE_MATCH == $eTag) {
					http_response_code(304);
					exit();
				}
			}
		}
	}

	protected function sendBody() {
		$fh = null;
		try {
			$fh = fopen($this->controllerResponse['url'], 'rb');
			while (!feof($fh)) {
				echo fread($fh, 1024 * 1024);
				ob_flush();
				flush();
			}
		} finally {
			if (is_resource($fh))
				fclose($fh);
		}
	}

}
