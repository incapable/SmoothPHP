<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Config.php
 * Simple data class containing configuration for this project.
 */

namespace SmoothPHP\Framework\Core;

use SmoothPHP\Framework\Core\Cron\Handlers\CronTab;

class Config {
	public $default_language = 'en_us';
	public $detect_language = true;

	public $date_format = 'l, d-M-Y H:i:s';
	public $image_inline_threshold = 10000;

	public $mysql_enabled = false;
	public $mysql_host = 'localhost';
	public $mysql_database = 'smoothphp';
	public $mysql_port = 3306;
	public $mysql_user = 'root';
	public $mysql_password = '';

	public $authentication_enabled = false;
	public $authentication_loginroute = null;
	public $authentication_rememberme = true;
	public $authentication_longlived_age = 604800; // 1 week by default

	public $cron_handler = CronTab::class;
	public $enable_robots = true;

	public $recaptcha_site_key = '';
	public $recaptcha_site_secret = '';
}
