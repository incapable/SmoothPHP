<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * AssetsRegister.php
 * Register for all assets, used to add more assets to the template without direct access to said template.
 */

namespace SmoothPHP\Framework\Cache\Assets;

use JShrink\Minifier;
use SmoothPHP\Framework\Cache\Builder\FileCacheProvider;
use SmoothPHP\Framework\Core\Kernel;
use tubalmartin\CSSmin\CSSmin;

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

        if (__ENV__ == 'dev') {
            $this->jsCache = new FileCacheProvider('js', null, array(AssetsRegister::class, 'simpleLoad'));
            $this->cssCache = new FileCacheProvider('css', null, array(AssetsRegister::class, 'simpleLoad'));
        } else {
            $this->jsCache = new FileCacheProvider('js', 'final.js', array(AssetsRegister::class, 'minifyJS'));
            $this->cssCache = new FileCacheProvider('css', 'final.css', array(AssetsRegister::class, 'minifyCSS'));
        }
        $this->imageCache = new ImageCache('images');

        $route = $kernel->getRouteDatabase();
        if ($route) {
            $route->register(array(
                'name' => 'assets_images',
                'path' => '/images/...',
                'controller' => AssetsController::class,
                'call' => 'getImage'
            ));

            if (__ENV__ != 'dev') {
                $route->register(array(
                    'name' => 'assets_css_compiled',
                    'path' => '/css/%/compiled.css',
                    'controller' => AssetsController::class,
                    'call' => 'getCompiledCSS'
                ));
                $route->register(array(
                    'name' => 'assets_js_compiled',
                    'path' => '/js/%/compiled.js',
                    'controller' => AssetsController::class,
                    'call' => 'getCompiledJS'
                ));
            } else {
                $route->register(array(
                    'name' => 'assets_js',
                    'path' => '/js/...',
                    'controller' => AssetsController::class,
                    'call' => 'getJS'
                ));
                $route->register(array(
                    'name' => 'assets_css',
                    'path' => '/css/...',
                    'controller' => AssetsController::class,
                    'call' => 'getCSS'
                ));
            }
        }
    }

    public static function getSourcePath($type, $file) {
        $path = sprintf('%ssrc/assets/%s/%s', __ROOT__, $type, $file);
        if (!file_exists($path))
            throw new \RuntimeException($type . " file '" . $file . "' does not exist.");
        return $path;
    }

    public function addJS($file) {
        $this->js[] = $file;
        if (strtolower(substr($file, 0, 4)) != 'http') {
            $path = self::getSourcePath('js', $file);
            $this->jsCache->fetch($path);
        }
    }

    public function getJSFiles() {
        return $this->js;
    }

    public function getJSPath($file) {
        return $this->jsCache->getCachePath(self::getSourcePath('js', $file));
    }

    public function addCSS($file) {
        $this->css[] = $file;
        if (strtolower(substr($file, 0, 4)) != 'http') {
            $path = self::getSourcePath('css', $file);
            $this->cssCache->fetch($path);
        }
    }

    public function getCSSPath($file) {
        return $this->cssCache->getCachePath(self::getSourcePath('css', $file));
    }

    public function getCSSFiles() {
        return $this->css;
    }

    public function getImage($file, $width = null, $height = null) {
        $cachePath = $this->imageCache->ensureCache(self::getSourcePath('images', $file), $width, $height);
        $fileInfo = pathinfo($file);

        $mimes = array(
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'png' => 'image/png'
        );

        global $kernel;
        if (__ENV__ != 'dev' && filesize($cachePath) <= $kernel->getConfig()->image_inline_threshold) {
            return sprintf('data:%s;base64,%s', $mimes[$fileInfo['extension']], base64_encode(file_get_contents($cachePath)));
        } else {
            $virtualPath = sprintf('/images/%s%s.%dx%d.%s',
                $fileInfo['dirname'] == '.' ? '' : ($fileInfo['dirname'] . '/'),
                $fileInfo['filename'],
                $width,
                $height,
                $fileInfo['extension']);

            return $virtualPath;
        }
    }

    public static function simpleLoad($filePath) {
        global $kernel;
        return $kernel->getTemplateEngine()->simpleFetch($filePath, array(
            'assets' => $kernel->getAssetsRegister(),
            'route' => $kernel->getRouteDatabase()
        ));
    }

    public static function minifyCSS($filePath) {
        return (new CSSmin())->run(self::simpleLoad($filePath));
    }

    public static function minifyJS($filePath) {
        return Minifier::minify(self::simpleLoad($filePath));
    }

}
