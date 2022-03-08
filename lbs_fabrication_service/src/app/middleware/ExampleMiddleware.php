<?php

namespace lbs\fab\app\middleware;

use \Slim\Container;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class ExampleMiddleware
{

    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    public function mw1(Request $req, Response $resp, callable $next) : Response 
    {

        // if (!$req->getQueryParam('token', null)) {
        //     $resp->getBody()->write('erreur : pas de token');
        //     return $resp;
        // }

        // Récupérer un attribut ajouté dans un middleware précédent. Est ignoré si mw2 pas invoqué.
        $messageFromMw2 = $req->getAttribute('msgFromMw2');

        $resp->getBody()->write('<h1> mw1 : before </h1>');
        $resp->getBody()->write($messageFromMw2 . ' from mw1');

        $resp = $next($req, $resp);

        $resp->getBody()->write('<h1> mw1 : after </h1>');
        $resp->getBody()->write($messageFromMw2 . ' from mw1');

        $resp = $resp->withStatus(203);


        return $resp;
    }

    public function mw2(Request $req, Response $resp, callable $next) : Response 
    {

        // if (!$req->getQueryParam('token', null)) {
        //     $resp->getBody()->write('erreur : pas de token');
        //     return $resp;
        // }

        $resp->getBody()->write('<h1> mw2 : before </h1>');

        // Passer un attribut au prochain middleware/fonction de route -> dans la requete
        $req = $req->withAttribute('msgFromMw2', '<h1>Mw2 says Hello</h1>');


        $resp = $next($req, $resp);

        $resp->getBody()->write('<h1> mw2 : after </h1>');

        // $resp = $resp->withStatus(203);

        return $resp;
    }

    public function mw3(Request $req, Response $resp, callable $next) : Response 
    {

        // Récupére la route de la requête
        $route = $req->getAttribute('route');

        $resp = $next($req, $resp);

        //                                             Récupére l'argument 'name' en query de la route
        $resp->getBody()->write('<h1> mw3 : after ' . $route->getArgument('name') . ' </h1>');

        return $resp;
    }
}