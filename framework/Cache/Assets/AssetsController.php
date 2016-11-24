<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * AssetsController.php
 * Controller used for all js/css/image accesses.
 */

namespace SmoothPHP\Framework\Cache\Assets;

use SmoothPHP\Framework\Flow\Responses\OctetStream;

class AssetsController {

    public function getJS(AssetsRegister $register, array $path) {
        $file = $register->getJSPath(implode('/', $path));

        return new OctetStream(array(
            'type' => 'text/javascript',
            'filename' => end($path),
            'url' => $file
        ));
    }

    public function getCSS(AssetsRegister $register, array $path) {
        $file = $register->getCSSPath(implode('/', $path));

        return new OctetStream(array(
            'type' => 'text/css',
            'filename' => end($path),
            'url' => $file
        ));
    }

    public function getImage(array $path) {
        preg_match('/^(.+?)(?:\.([0-9]+?)x([0-9]+?))?\.([a-z]+)$/', implode('/', $path), $matches);

        $srcFile = sprintf('src/assets/images/%s.%s', $matches[1], $matches[4]);
        $srcFileFull = __ROOT__ . $srcFile;
        $cacheFile = sprintf('%scache/images/%dx%d.%s.%s.%s',
            __ROOT__,
            $matches[2],
            $matches[3],
            str_replace(array('/', '\\'), array('_', '_'), $srcFile),
            md5_file($srcFileFull),
            $matches[4]);

        return new OctetStream(array(
            'type' => image_type_to_mime_type(exif_imagetype($srcFileFull)),
            'filename' => end($path),
            'url' => $cacheFile
        ));
    }

}