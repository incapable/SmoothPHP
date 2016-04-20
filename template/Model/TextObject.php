<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * TextObject.php
 * Description
 */

namespace Test\Model;

use SmoothPHP\Framework\Database\Mapper\MappedMySQLObject;

class TextObject extends MappedMySQLObject {
    private $text;

    public function getTableName() {
        return 'table';
    }

    public function setText($text) {
        $this->text = $text;
    }
}