<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * ResolvedRoute.php
 * Class representing a route found after parsing the request.
 */

namespace SmoothPHP\Framework\Flow\Routing;

use SmoothPHP\Framework\Core\Kernel;
use SmoothPHP\Framework\Flow\Requests\Request;
use SmoothPHP\Framework\Flow\Responses\Response;

class ResolvedRoute {
    private $route;
    private $parameters;

    public function __construct(array &$route, array $parameters) {
        $this->route = $route;
        $this->parameters = $parameters;
    }

    public function buildResponse(Kernel $kernel, Request $request) {
        if ($kernel->getConfig()->authentication_enabled) {
            $authResponse = $kernel->getAuthenticationManager()->verifyAccess($request, $this->route, $this->parameters);
            if ($authResponse instanceof Response) {
                $authResponse->build($kernel, $request);
                return $authResponse;
            }
        }

        $response = $this->route['controllercall']->performCall($kernel, $request, $this->parameters);

        if (!($response instanceof Response))
            $response = new $this->route['content-type']($response);

        $response->build($kernel, $request);
        return $response;
    }
}