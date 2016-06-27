<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * FormContainer.php
 * Description
 */

namespace SmoothPHP\Framework\Forms\Containers;

use SmoothPHP\Framework\Forms\Form;

class FormHeader {
    private $form;

    public function __construct(Form $form) {
        $this->form = $form;
    }

    public function __toString() {
        return '<form action="' . $this->form->getAction() . '" method="post" class="smoothform">';
    }

}