<?php

/**
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * **********
 * Copyright © 2015-2019
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * **********
 * MappedDBObject.php
 */

namespace SmoothPHP\Framework\Database\Mapper;

class MappedDBObject {
	public $id = 0;

	public function getTableName() {
		return strtolower((new \ReflectionClass($this))->getShortName());
	}

	public function getId() {
		return $this->id;
	}
}