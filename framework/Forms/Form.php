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

use SmoothPHP\Framework\Flow\Requests\Request;
use SmoothPHP\Framework\Forms\Containers\FormContainer;
use SmoothPHP\Framework\Forms\Containers\FormHeader;

class Form extends FormContainer {
    private $action;

    private $hasResult;
    private $failReasons;

    public function __construct($action, array $elements) {
        parent::__construct(array(
            'header' => new FormHeader($this),
            'tablestart' => '<table>',
            'inputs' => new FormContainer($elements),
            'tableend' => '</table>',
            'footer' => '</form>'
        ));
        $this->hasResult = false;
        $this->action = $action;
    }

    public function getAction() {
        return $this->action;
    }

    public function setAction() {
        global $kernel;
        $action = func_get_arg(0);

        if ($kernel->getRouteDatabase()->getRoute($action))
            $this->action = call_user_func_array(array($kernel->getRouteDatabase(), 'buildPath'), func_get_args());
        else
            $this->action = $action;
    }

    public function validate(Request $request) {
        if (!$request->post->_token)
            return;

        $this->hasResult = true;
        $this->failReasons = array();
        $this->checkConstraint($request, null, null, $this->failReasons);
    }

    public function hasResult() {
        return $this->hasResult;
    }

    public function isValid() {
        if (!$this->hasResult)
            return true;
        return isset($this->failReasons) && count($this->failReasons) == 0;
    }

    public function getErrorMessages() {
        return $this->failReasons;
    }
    
}