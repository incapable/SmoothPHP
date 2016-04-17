<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * DereferenceOperatorElement.php
 * Description
 */

namespace SmoothPHP\Framework\Templates\Elements\Operators;

use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\Compiler\TemplateCompileException;
use SmoothPHP\Framework\Templates\Elements\Element;
use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;

class DereferenceOperatorElement extends Element {
    private $left, $right;

    public function __construct(Element $left) {
        $this->left = $left;
    }

    public function setRight(Element $right) {
        $this->right = $right;
    }

    public function optimize(CompilerState $tpl) {
        $this->left = $this->left->optimize($tpl);
        if (!($this->right) instanceof FunctionOperatorElement)
            $this->right = $this->right->optimize($tpl);
        else if ($tpl->performCalls)
            return new PrimitiveElement(call_user_func_array(array($this->left->getValue(), $this->right->getFunctionName()), $this->right->getPrimitiveArgs($tpl)));
        return $this;
    }

    public function output(CompilerState $tpl) {
        $this->optimize($tpl);

        if (!($this->left instanceof PrimitiveElement))
            throw new TemplateCompileException("Could not determine left-hand of '->' at runtime.");
        else {
            if ($this->right instanceof PrimitiveElement)
                echo $this->left->getValue()->{$this->right->getValue()};
            else if ($this->right instanceof FunctionOperatorElement)
                echo call_user_func_array(array($this->left->getValue(), $this->right->getFunctionName()), $this->right->getPrimitiveArgs($tpl));
            else
                throw new TemplateCompileException("Right-hand of '->' is invalid.");
        }
    }
}