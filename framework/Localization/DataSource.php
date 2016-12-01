<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * DataSource.php
 * Interface used by LanguageRepository to get language entries.
 */

namespace SmoothPHP\Framework\Localization;

interface DataSource {

    public function getEntry($language, $key);

}