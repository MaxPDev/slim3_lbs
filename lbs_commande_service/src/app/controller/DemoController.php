<?php

namespace lbs\command\app\controller;

use \Psr\Http\Message\ServerRequestInterface as Request ;
use \Psr\Http\Message\ResponseInterface as Response ;

use \Slim\Container;

class DemoController {

    private $container; // container de dépendances de l'app

    public function __construct(Container $container) {
        $this->container = $container;
    }

    function sayHello (Request $rq, Response $rs, array $args) : Response {

        $p = $rq->getQueryParam('p', 0);
        $name = $args['name'];
        
        // $dbfile = $this->settings['dbfile']; s'il était dans index.php.
        // comme on a injecté le conteneur, c'est $this->container
        
        $dbfile = $this->container->settings['dbfile'];

        $rs->getBody()->write("<h1>Hello, $name</h1><h2>$dbfile : $dbfile</h2><h2>p = $p</h2>");
        return $rs;
    }

}
