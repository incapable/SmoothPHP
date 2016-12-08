<?php

namespace Test\Controllers;

use SmoothPHP\Framework\Authentication\AuthenticationManager;
use SmoothPHP\Framework\Core\Abstracts\Controller;
use SmoothPHP\Framework\Flow\Responses\TemplateResponse;
use SmoothPHP\Framework\Flow\Requests\Request;

class TestController extends Controller {

    public function login(AuthenticationManager $auth, Request $request) {
        if ($auth->checkLoginResult($request))
            return 'Successfully logged in!';

        return new TemplateResponse('test.tpl', array(
            'form' => $auth->getLoginForm($request)
        ));
    }

}
