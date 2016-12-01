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
        $languages = array();
        foreach($this->sources as $source)
            $languages = array_merge($languages, $source->getAvailableLanguages());
        if (!in_array($language, $languages))
            throw new \RuntimeException("Language '" . $language . "' could not be found.");

        $_SESSION[self::SESSION_KEY] = $language;
        return true;
    }

    public function getEntry($key, $language = null) {
        $language = $language ?: $this->detectLanguage();
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

    private function detectLanguage() {
        if (isset($_SESSION[self::SESSION_KEY]))
            return $_SESSION[self::SESSION_KEY];

        $language = $this->kernel->getConfig()->default_language;

        if ($this->kernel->getConfig()->detect_language) {
            $languages = array();
            foreach($this->sources as $source)
                $languages = array_unique(array_merge($languages, $source->getAvailableLanguages()));

            $language = locale_lookup($languages,
                                      locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']),
                                      true,
                                      $this->kernel->getConfig()->default_language);
        }

        $_SESSION[self::SESSION_KEY] = $language;
        return $language;
    }
}