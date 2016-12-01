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
    const SESSION_KEY = 'sm_language';

    private $kernel;
    private $sources;

    public function __construct(Kernel $kernel) {
        $this->kernel = $kernel;
        $this->sources = array();
    }

    public function addSource(DataSource $source) {
        array_unshift($this->sources, $source);
    }

    public function setSessionLanguage($language) {
        $found = false;
        foreach($this->sources as $source)
            if ($source->checkLanguage($language))
                $found = true;

        if (!$found)
            throw new \RuntimeException('Unknown language: ' . $language);

        $_SESSION[self::SESSION_KEY] = $language;
    }

    public function getEntry($key, $language = null) {
        $language = $language ?: (isset($_SESSION[self::SESSION_KEY]) ? $_SESSION[self::SESSION_KEY] : $this->kernel->getConfig()->default_language);
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