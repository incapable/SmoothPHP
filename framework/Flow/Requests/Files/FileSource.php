<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * FileSource.php
 * A "source" for all files uploaded with a form.
 */

namespace SmoothPHP\Framework\Flow\Requests\Files;

class FileSource {
	private $source;

	public function __construct(array $source) {
		$this->source = [];

		foreach ($source as $name => $element)
			$this->source[$name] = new FileElement($element);
	}

	/**
	 * @param $name
	 * @return FileElement|bool
	 */
	public function __get($name) {
		if (!isset($this->source[$name]))
			return false;

		return $this->source[$name];
	}
}