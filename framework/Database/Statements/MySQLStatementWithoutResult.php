<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * MySQLStatementWithoutResult.php
 * Prepared MySQL statement that does not produce a result
 */

namespace SmoothPHP\Framework\Database\Statements;


class MySQLStatementWithoutResult extends MySQLStatement {

    public function createResult() {
        $this->stmt->store_result();

        $id = $this->stmt->insert_id;

        $this->stmt->free_result();
        $this->stmt->reset();

        return $id;
    }

}
