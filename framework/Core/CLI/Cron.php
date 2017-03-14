<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Cron.php
 * Handles cron tasks to use by this project.
 */

namespace SmoothPHP\Framework\Core\CLI;

use SmoothPHP\Framework\Core\Cron\CronManager;
use SmoothPHP\Framework\Core\Kernel;

class Cron extends Command {

    public function getDescription() {
        return 'Handles cron tasks to use by this project.';
    }

    public function handle(Kernel $kernel, array $argv) {
        global $website;

        $mgr = new CronManager($kernel);
        $kernel->registerCron($mgr);
        $website->registerCron($mgr);

        if (count($argv) > 1) {
            switch ($argv[0]) {
                case 'install':
                    $mgr->install();
                    return;
                case 'uninstall':
                    $mgr->uninstall();
                    return;
                case 'run':
                    if (count($argv) > 2) {
                        $mgr->run($kernel, $argv[1]);
                        return;
                    }
            }
        }

        print('Usage: smoothphp cron <install|uninstall|run> [jobname]' . PHP_EOL);
    }

}
