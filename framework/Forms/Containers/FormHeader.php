<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * FormHeader.php
 * Header for all forms that includes the form validation token.
 */

namespace SmoothPHP\Framework\Forms\Containers;

use SmoothPHP\Framework\Flow\Requests\Request;
use SmoothPHP\Framework\Forms\Constraint;
use SmoothPHP\Framework\Forms\Form;

class FormHeader extends Constraint {

    const SESSION_KEY = 'sm_formtokens';

    private $form;

    public function __construct(Form $form) {
        $this->form = $form;
    }

    public function __toString() {
        if (!isset($_SESSION[self::SESSION_KEY]))
            $_SESSION[self::SESSION_KEY] = array();

        $formToken = md5(uniqid(rand(), true));
        $_SESSION[self::SESSION_KEY][] = $formToken;

        return '<form action="' . $this->form->getAction() . '" method="post" class="smoothform">'
            . '<input type="hidden" id="_token" name="_token" value="' . $formToken . '" />';
    }

    public function checkConstraint(Request $request, $name, $value, array &$failReasons) {
        $key = array_search($request->post->_token, $_SESSION[self::SESSION_KEY], true);
        if ($key === false) {
            $failReasons[] = 'Form security token mismatch.';
            return;
        }
        unset($_SESSION[self::SESSION_KEY][$key]);
    }

}