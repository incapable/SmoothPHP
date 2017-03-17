<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * MySQLStatementWithoutResult.php
 * Prepared MySQL statement that does not produce a result
 */

namespace SmoothPHP\Framework\Database\Statements;


class MySQLStatementWithoutResult extends MySQLStatement {

    public function createResult() {
        $stmt = $this->getMySQLi_stmt();

        $stmt->store_result();

        $id = $stmt->insert_id;

        $stmt->free_result();
        $stmt->reset();

        return $id;
    }

}
