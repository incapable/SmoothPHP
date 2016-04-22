<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * AssetsRegister.php
 * Description
 */

namespace SmoothPHP\Framework\Cache\Assets;

use SmoothPHP\Framework\Cache\Builder\FileCacheProvider;
use SmoothPHP\Framework\Core\Kernel;

class AssetsRegister {
    private $jsCache, $cssCache, $imageCache;
    private $js, $css;

    public function __construct(Kernel $kernel) {
        $this->js = array();
        $this->css = array();

        $this->jsCache = new FileCacheProvider("js");
        $this->cssCache = new FileCacheProvider("css");
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
        return $this->cssCache->getCachePath($path);
    }

    public function getCSSFiles() {
        return $this->css;
    }

    public function getImage($file, $x = -1, $y = -1) {

    }
}