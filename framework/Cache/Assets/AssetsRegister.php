<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * AssetsRegister.php
 * Register for all assets, used to add more assets to the template without direct access to said template.
 */

namespace SmoothPHP\Framework\Cache\Assets;

use SmoothPHP\Framework\Cache\Builder\FileCacheProvider;
use SmoothPHP\Framework\Core\Kernel;

class AssetsRegister {
    private $jsCache, $cssCache, $imageCache;
    private $js, $css;

    public function initialize(Kernel $kernel) {
        $this->js = array();
        $this->css = array();

        $parser = function($filePath) use ($kernel) {
            return $kernel->getTemplateEngine()->simpleFetch($filePath, array(
                'assets' => $kernel->getAssetsRegister(),
                'route' => $kernel->getRouteDatabase()
            ));
        };

        $this->jsCache = new FileCacheProvider("js", null, $parser);
        $this->cssCache = new FileCacheProvider("css", null, $parser);
        $this->imageCache = new FileCacheProvider("images", "img");

        $route = $kernel->getRouteDatabase();
        $route->register(array(
            'name' => 'assets_js',
            'path' => '/javascript/...',
            'controller' => AssetsController::class,
            'call' => 'getJS'
        ));
        $route->register(array(
            'name' => 'assets_css',
            'path' => '/css/...',
            'controller' => AssetsController::class,
            'call' => 'getCSS'
        ));
        $route->register(array(
            'name' => 'assets_images',
            'path' => '/images/...',
            'controller' => AssetsController::class,
            'call' => 'getImage'
        ));
    }

    public function addJS($file) {
        $path = sprintf('%ssrc/assets/js/%s', __ROOT__, $file);
        if (file_exists($path)) {
            $this->jsCache->fetch($path);
            $this->js[] = $file;
        } else
            throw new \RuntimeException("Javascript file '" . $file . "' does not exist.");
    }

    public function getJSFiles() {
        return $this->js;
    }

    public function getJSPath($file) {
        $path = sprintf('%ssrc/assets/js/%s', __ROOT__, $file);
        if (!file_exists($path))
            throw new \RuntimeException();
        return $this->jsCache->getCachePath($path);
    }

    public function addCSS($file) {
        $path = sprintf('%ssrc/assets/css/%s', __ROOT__, $file);
        if (file_exists($path)) {
            $this->cssCache->fetch($path);
            $this->css[] = $file;
        } else
            throw new \RuntimeException("CSS file '" . $file . "' does not exist.");
    }

    public function getCSSPath($file) {
        $path = sprintf('%ssrc/assets/css/%s', __ROOT__, $file);
        if (!file_exists($path))
            throw new \RuntimeException();
        return $this->cssCache->getCachePath($path);
    }

    public function getCSSFiles() {
        return $this->css;
    }

    public function getImage($file, $width = null, $height = null) {
        $path = sprintf('%ssrc/assets/images/%s', __ROOT__, $file);
        if (file_exists($path)) {
            $isOriginalSize = false;
            list($originalWidth, $originalHeight) = getimagesize($path);
            if ($width == null && $height != null)
                $width = $height * ($originalWidth / $originalHeight);
            else if ($width != null && $height == null)
                $height = $width * ($originalHeight / $originalWidth);
            else if ($width == null && $height == null) {
                $width = $originalWidth;
                $height = $originalHeight;
                $isOriginalSize = true;
            }

            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $this->imageCache->fetch($path, function($filePath) use ($ext, $width, $height, $originalWidth, $originalHeight, $isOriginalSize) {
                // Lambda: Cache builder
                if ($isOriginalSize)
                    return null; // This will instruct the saving mechanism to make a symlink instead

                $source = call_user_func( sprintf( 'imagecreatefrom%s', $ext == 'jpg' ? 'jpeg' : $ext ), $filePath );
                $target = imagecreatetruecolor($width, $height);

                // Make the image transparent to begin with
                imagealphablending($target, false);
                imagesavealpha($target, true);
                $transparent = imagecolorallocatealpha($target, 255, 255, 255, 127);
                imagefilledrectangle($target, 0, 0, $width, $height, $transparent);

                // Copy the old image in, sampling it
                imagecopyresampled($target, $source, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);

                // Clean up the source
                imagedestroy($source);

                return $target;
            }, function($cacheFile) {
                // Lambda: Cache reader
                return null; // Since we don't actually use the file here, don't bother reading it
            }, function($cacheFile, $image) use ($path, $ext, $width, $height) {
                // Lambda: Cache writer
                $fileInfo = pathinfo($cacheFile);
                $cacheFile = sprintf('%s/%s.%dx%d.%s', $fileInfo['dirname'], $fileInfo['filename'], $width, $height, $ext);

                if ($image == null) {
                    // Create a link
                    symlink($path, $cacheFile);
                } else {
                    imagepng($image, $cacheFile, 9);
                    imagedestroy($image);
                }
            });

            $fileInfo = pathinfo($file);
            $virtualPath = sprintf('/images/%s%s.%dx%d.%s', $fileInfo['dirname'] == '.' ? '' : ($fileInfo['dirname'] . '/'), $fileInfo['filename'], $width, $height, $fileInfo['extension']);

            return $virtualPath;
        } else
            throw new \RuntimeException("Image '" . $file . "' does not exist.");
    }

}