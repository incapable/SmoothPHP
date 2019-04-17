<?php

/**
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * **********
 * Copyright © 2015-2019
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * **********
 * JSON.php
 */

namespace SmoothPHP\Framework\Flow\Responses;

use SmoothPHP\Framework\Core\Kernel;
use SmoothPHP\Framework\Flow\Requests\Request;

class JSON extends Response implements AlternateErrorResponse {
	private $built;
	private $gzip;

	public function buildErrorResponse($message) {
		$this->controllerResponse = [
			'success' => false,
			'error'   => $message
		];
	}

	public function build(Kernel $kernel, Request $request) {
		$this->built = json_encode($this->controllerResponse);
		if ($this->built === false)
			throw new \RuntimeException('Could not encode json: ' . json_last_error_msg());

		if (strpos($request->server->HTTP_ACCEPT_ENCODING, 'gzip') !== false) {
			$this->gzip = true;
			$this->built = gzencode($this->built);
		}
	}

	protected function sendHeaders() {
		parent::sendHeaders();
		header('Content-Type: application/json; charset=utf-8');
		header('Vary: Accept-Encoding');

		if ($this->gzip)
			header('Content-Encoding: gzip');
	}

	protected function sendBody() {
		echo $this->built;
	}

}