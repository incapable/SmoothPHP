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
    private $attributes;

    public function __construct(Form $form, array $attributes) {
        $this->form = $form;

        $this->attributes = array_replace_recursive(array(
            'token' => true,
            'attr' => array(
                'method' => 'post',
                'class' => 'smoothform',
                'enctype' => 'multipart/form-data'
            )
        ), $attributes);
    }

    public function __toString() {
        $tokenInput = '';
        if ($this->attributes['token']) {
            if (!isset($_SESSION[self::SESSION_KEY]))
                $_SESSION[self::SESSION_KEY] = array();

            $formToken = md5(uniqid(rand(), true));
            $_SESSION[self::SESSION_KEY][] = $formToken;

            $tokenInput = sprintf('<input type="hidden" id="_token" name="_token" value="%s" />', $formToken);
        }

        $htmlAttributes = array();
        $attributes = $this->attributes['attr'];

        $attributes['action'] = $this->form->getAction();

        foreach($attributes as $key => $attribute)
            if (isset($attribute) && strlen($attribute) > 0)
                $htmlAttributes[] = sprintf('%s="%s"', $key, addcslashes($attribute, '"'));

        return sprintf('<form %s />%s', implode(' ', $htmlAttributes), $tokenInput);
    }

    public function checkConstraint(Request $request, $name, $label, $value, array &$failReasons) {
        if ($this->attributes['token']) {
            $key = array_search($request->post->_token, $_SESSION[self::SESSION_KEY], true);
            if ($key === false) {
                $failReasons[] = 'Form security token mismatch.';
                return;
            }
            unset($_SESSION[self::SESSION_KEY][$key]);
        }
    }

}