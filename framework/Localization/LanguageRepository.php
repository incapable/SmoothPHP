<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * LanguageRepository.php
 * Entrypoint for all language queries.
 */

namespace SmoothPHP\Framework\Localization;

use SmoothPHP\Framework\Core\Kernel;

class LanguageRepository {
    private $kernel;
    private $sources;

    public function __construct(Kernel $kernel) {
        $this->kernel = $kernel;
        $this->sources = array();
    }

    public function addSource(DataSource $source) {
        array_unshift($this->sources, $source);
    }

    public function getEntry($key, $language = null) {
        $language = $language ?: (isset($_SESSION['language']) ? $_SESSION['language'] : $this->kernel->getConfig()->default_language);
        foreach($this->sources as $source) {
            $entry = $source->getEntry($language, $key);
            if ($entry)
                return $entry;
        }

        return false;
    }

    public function __get($name) {
        return $this->getEntry($name);
    }
}