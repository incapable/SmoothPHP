<?php

namespace SmoothPHP\Framework\Core\Cron;

abstract class CronHandler {

    public abstract function save(array $jobs);

}