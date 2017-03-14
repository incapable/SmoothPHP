<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * AssetsController.php
 * Controller used for all js/css/image accesses.
 */

namespace SmoothPHP\Framework\Cache\Assets;

use SmoothPHP\Framework\Core\Abstracts\Controller;
use SmoothPHP\Framework\Flow\Responses\FileStream;

class AssetsController extends Controller {

    public function getJS(AssetsRegister $register, array $path) {
        $file = $register->getJSPath(implode('/', $path));

        return new FileStream(array(
            'cache' => true,
            'type' => 'text/javascript',
            'filename' => end($path),
            'url' => $file
        ));
    }

    public function getCSS(AssetsRegister $register, array $path) {
        $file = $register->getCSSPath(implode('/', $path));

        return new FileStream(array(
            'cache' => true,
            'type' => 'text/css',
            'filename' => end($path),
            'url' => $file
        ));
    }

    public function getImage(array $path) {
        preg_match('/^(.+?)(?:\.([0-9]+?)x([0-9]+?))?\.([a-z]+)$/', implode('/', $path), $matches);

        $srcFile = sprintf('src/assets/images/%s.%s', $matches[1], $matches[4]);
        $srcFileFull = __ROOT__ . $srcFile;
        $cacheFile = sprintf('%scache/images/%s.%dx%d.%s.%s',
            __ROOT__,
            str_replace(array('/', '\\'), array('_', '_'), $srcFile),
            $matches[2],
            $matches[3],
            md5_file($srcFileFull),
            $matches[4]);

        return new FileStream(array(
            'cache' => true,
            'type' => image_type_to_mime_type(exif_imagetype($srcFileFull)),
            'filename' => end($path),
            'url' => $cacheFile
        ));
    }

}