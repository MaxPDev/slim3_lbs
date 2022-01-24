<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

$app = new \Slim\App;

$app->get('/commandes/[id][/]',
    function (Request $req, Response $resp, $args) {
        $id = $args['id'];
        $resp = $resp->withAddedHeader( 'Content-Type', 'application/json;charset=utf-8' );
        $resp->getBody()->write(json_encode("<h1>Oui</h1>"));
        return $resp;
    }
);

$app->get('/commandes[/]',
    function (Request $req, Response $resp) {
        $resp = $resp->withAddedHeader( 'Content-Type', 'application/json;charset=utf-8' );
        $resp->getBody()->write(json_encode("<h1>Oui</h1>"));
        return $resp;
    }
);

$app->run();
