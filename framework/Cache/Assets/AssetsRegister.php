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
    /* @var $jsCache FileCacheProvider */
    private $jsCache;
    /* @var $cssCache FileCacheProvider */
    private $cssCache;
    /* @var $imageCache ImageCache */
    private $imageCache;
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

        $this->jsCache = new FileCacheProvider('js', null, $parser);
        $this->cssCache = new FileCacheProvider('css', null, $parser);
        $this->imageCache = new ImageCache('images');

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
            $this->imageCache->ensureCache($path, $width, $height);

            $fileInfo = pathinfo($file);
            $virtualPath = sprintf('/images/%s%s.%dx%d.%s',
                $fileInfo['dirname'] == '.' ? '' : ($fileInfo['dirname'] . '/'),
                $fileInfo['filename'],
                $width,
                $height,
                $fileInfo['extension']);

            return $virtualPath;
        } else
            throw new \RuntimeException("Image '" . $file . "' does not exist.");
    }

}