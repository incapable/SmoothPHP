<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * ImageCache.php
 * A specialised cache for images with support for resampling.
 */

namespace SmoothPHP\Framework\Cache\Assets;

use SmoothPHP\Framework\Core\Lock;
use SmoothPHP\Framework\Cache\Builder\FileCacheProvider;

class ImageCache {
    private $cacheFileFormat;

    public function __construct($folder) {
        $this->cacheFileFormat = __ROOT__ . 'cache/' . $folder . '/%s.%dx%d.%s.%s';
    }

    public function ensureCache($image, &$width = null, &$height = null) {
        $isOriginal = $this->determineSizes($image, $width, $height, $originalWidth, $originalHeight);
        $cacheFile = $this->getCachePath($image, $width, $height, $fileName);

        if (!is_dir(dirname($cacheFile)))
            mkdir(dirname($cacheFile), FileCacheProvider::PERMS, true);

        // Check if the cache exists
        if (file_exists($cacheFile))
            return;

        // If we get to this point, the above return has not returned.
        // Which means we have to generate a new cache
        $lock = new Lock(pathinfo($cacheFile, PATHINFO_BASENAME));

        if ($lock->lock()) {
            array_map('unlink', glob(sprintf($this->cacheFileFormat, $fileName, $width, $height, '*', pathinfo($image, PATHINFO_EXTENSION))));

            if ($isOriginal) {
                // Create a symlink
                symlink($image, $cacheFile);
            } else {
                // Generate a resized image file
                $ext = pathinfo($image, PATHINFO_EXTENSION);
                $source = call_user_func( sprintf( 'imagecreatefrom%s', $ext == 'jpg' ? 'jpeg' : $ext ), $image );
                $target = imagecreatetruecolor($width, $height);

                // Make the image transparent to begin with
                imagealphablending($target, false);
                imagesavealpha($target, true);
                $transparent = imagecolorallocatealpha($target, 255, 255, 255, 127);
                imagefilledrectangle($target, 0, 0, $width, $height, $transparent);

                // Copy the old image in, sampling it
                imagecopyresampled($target, $source, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);

                // Write the new image
                imagepng($target, $cacheFile, 9);

                // Clean up the source
                imagedestroy($source);
                imagedestroy($target);
            }

            $lock->unlock();
            return;
        }
    }

    public function determineSizes($path, &$width = null, &$height = null, &$originalWidth = null, &$originalHeight = null) {
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

        return $isOriginalSize;
    }

    public function getCachePath($sourceFile, $width, $height, &$fileName = null) {
        $fileName = str_replace(array('/', '\\'), array('_', '_'), str_replace(__ROOT__, '', $sourceFile));
        $checksum = md5_file($sourceFile);

        return sprintf($this->cacheFileFormat, $fileName, $width, $height, $checksum, pathinfo($sourceFile, PATHINFO_EXTENSION));
    }

}