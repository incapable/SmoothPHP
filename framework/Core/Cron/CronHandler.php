<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * CronHandler.php
 * Interface describing different handler types for Cron, such as unix's crontab.
 */

namespace SmoothPHP\Framework\Core\Cron;

abstract class CronHandler {

    public abstract function save(array $jobs);

}