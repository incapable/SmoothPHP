<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * FormContainer.php
 * Container for array elements that will pass on calls to each individual element.
 */

namespace SmoothPHP\Framework\Forms\Containers;

use SmoothPHP\Framework\Flow\Requests\Request;
use SmoothPHP\Framework\Forms\Constraint;

class FormContainer extends Constraint {
    private $backing;

    public function __construct(array $backing) {
        $this->backing = $backing;
    }

    public function __get($name) {
        return $this->backing[$name];
    }

    public function __iterate() {
        return $this->backing;
    }

    public function __call($method, $args) {
        foreach($this->backing as $sub)
            if (method_exists($sub, $method))
                return call_user_func_array(array($sub, $method), $args);

        throw new \RuntimeException(sprintf('The method %s::%s does not exist.', __CLASS__, $method));
    }

    public function __toString() {
        $result = '';
        foreach($this->backing as $element)
            $result .= $element;
        return $result;
    }

    public function checkConstraint(Request $request, $name, $value, array &$failReasons) {
        foreach($this->backing as $element)
            if ($element instanceof Constraint) {
                if ($element instanceof Type)
                    $value = $request->post->get($element->getFieldName());
                $element->checkConstraint($request, null, $value, $failReasons);
            }
    }

}