<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * MySQLStatementWithoutResult.php
 * Description
 */

namespace SmoothPHP\Framework\Database;


class MySQLStatementWithoutResult extends MySQLStatement {

    public function createResult() {
        return $this->stmt->affected_rows;
    }

}