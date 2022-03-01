<?php

namespace lbs\command\app\middleware;

use \Slim\Container;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class Middleware
{

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function checkToken(Request $req, Response $resp, callable $next): Response
    {
        //
        return $req;
    }
}
