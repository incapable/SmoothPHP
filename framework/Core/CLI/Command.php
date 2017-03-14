<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Command.php
 * Abstract class representing a CLI command implementation.
 */

namespace SmoothPHP\Framework\Core\CLI;

use SmoothPHP\Framework\Core\Kernel;

abstract class Command {

    public abstract function getDescription();

    public abstract function handle(Kernel $kernel, array $argv);

    protected function traverse( $folder, $action, $depth = -1 ) {
        $contents = @scandir( $folder );
        if ( !$contents )
            return;

        foreach($contents as $file) {
            if ( $file != '.' && $file != '..' ) {
                if ( is_dir( $folder . '/' . $file ) ) {
                    if ( $depth != 0 )
                        $this->traverse( $folder . '/' . $file, $action, $depth - 1 );
                    call_user_func( $action, $folder . '/' . $file, true );
                } else {
                    call_user_func( $action, $folder . '/' . $file, false );
                }
            }
        }
    }

}