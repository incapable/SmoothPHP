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
        if (!file_exists($file))
            throw new \Error();

        return new OctetStream(array(
            'type' => 'text/javascript',
            'filename' => end($path),
            'url' => $file
        ));
    }

    public function getCSS(AssetsRegister $register, array $path) {
        $file = $register->getCSSPath(implode('/', $path));
        if (!file_exists($file))
            throw new \Error();

        return new OctetStream(array(
            'type' => 'text/css',
            'filename' => end($path),
            'url' => $file
        ));
    }

    public function getImage(array $path) {
        $file = sprintf('%scache/images/%s', __ROOT__, implode('/', $path));
        if (!file_exists($file))
            throw new \Error();

        return new OctetStream(array(
            'type' => image_type_to_mime_type(exif_imagetype($file)),
            'filename' => end($path),
            'url' => $file
        ));
    }

}