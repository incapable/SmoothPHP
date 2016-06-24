<?php

namespace Test\Controllers;

use SmoothPHP\Framework\Flow\Responses\TemplateResponse;
use SmoothPHP\Framework\Forms\FormBuilder;
use SmoothPHP\Framework\Forms\Types as Types;

class TestController {

    public function index() {
        $builder = new FormBuilder();
        $builder->add('username', Types\StringType::class);
        $builder->add('password', Types\PasswordType::class);
        $builder->add('submit', Types\SubmitType::class);

        return new TemplateResponse('test.tpl', array(
            'form' => $builder->getForm()
        ));
    }

}
