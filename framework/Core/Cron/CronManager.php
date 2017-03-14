<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * CronManager.php
 * Class managing all known cron jobs, installing and running them
 */

namespace SmoothPHP\Framework\Core\Cron;

use SmoothPHP\Framework\Core\Kernel;

class CronManager {
    /* @var \SmoothPHP\Framework\Core\Cron\CronHandler */
    private $handler;
    private $jobs;

    public function __construct(Kernel $kernel) {
        $handlerClass = $kernel->getConfig()->cron_handler;
        $this->handler = new $handlerClass();
        $this->jobs = array();
    }

    public function newJob($jobname, $timer, $callable) {
        $this->jobs[$jobname] = new Job($jobname, $timer, $callable);
    }

    public function install() {
        $this->handler->save($this->jobs);
    }

    public function uninstall() {
        $this->handler->save(array());
    }

    public function run(Kernel $kernel, $name) {
        $this->jobs[$name]->run($kernel);
    }

}