<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * MySQLStatementWithResult.php
 * Prepared MySQL statement that produces a result
 */

namespace SmoothPHP\Framework\Database\Statements;

use SmoothPHP\Framework\Database\MySQLResult;

class MySQLStatementWithResult extends MySQLStatement {

    public function createResult() {
        $resultList = array();

        $result = $this->stmt->get_result();

        while ($data = $result->fetch_assoc())
            $resultList[] = $data;

        $this->stmt->free_result();
        $this->stmt->reset();

        return new MySQLResult($resultList);
    }

}