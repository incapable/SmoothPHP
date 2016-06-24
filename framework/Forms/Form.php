<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Form.php
 * Description
 */

namespace SmoothPHP\Framework\Forms;

use SmoothPHP\Framework\Forms\Containers\FormContainer;

class Form extends FormContainer {

    public function __construct(array $elements) {
        parent::__construct(array(
            'header' => '<form action="" method="post" class="smoothform">',
            'tablestart' => '<table>',
            'inputs' => new FormContainer($elements),
            'tableend' => '</table>',
            'footer' => '</form>'
        ));
    }
    
}