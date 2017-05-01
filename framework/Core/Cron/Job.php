<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Job.php
 * Class representing an individual cron job, not to be used by the developer
 */

namespace SmoothPHP\Framework\Core\Cron;

use SmoothPHP\Framework\Core\Kernel;

class Job {
	const CRONTAB_FORMAT = '%s %ssmoothphp cron run %s';

	private $jobname;
	private $timer;
	private $callable;

	public function __construct($jobname, $timer, $callable) {
		$this->jobname = $jobname;
		$this->timer = $timer;
		$this->callable = $callable;
	}

	public function getName() {
		return $this->jobname;
	}

	public function getTimer() {
		return $this->timer;
	}

	public function run(Kernel $kernel) {
		call_user_func($this->callable, $kernel);
	}
}