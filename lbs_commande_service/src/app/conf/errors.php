<?php

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

return [

    // 405 : mon url, mauvaise méthode

    // 404, application web classoqie
    // 'notFoundHandler' => function(Container $container) {
    //     return function (Request $req, Response $resp) : Response {
    //         $resp->getBody()->write("<h1>erreur : route non trouvée</h1>");
    //         return $resp;
    //     };
    // },

    // Pour les api, utiliser plutôt erreur 400, et modifier l'header
    // aficher l'uri
    // logger l'erreur
    'notFoundHandler' => function (Container $container) {

        // use : transmettre en incluant le container dans la closure, reste dans 'l'environnement' de la fonction'
        // On donne à la fonction l'accès  à l'objet
        // Genre passage d'argument sans l'être (ou injection en paramètre privé d'une classe)
        return function (Request $req, Response $resp) use($container): Response {

            // Récupérer l'erreur
            $uri = $req->getUri();

            // Composer la requête, changer le status (par déf 404)
            // typer en JSON
            $resp = $resp->withStatus(400)
                         ->withHeader('Content-Type', 'application/json');
            $resp->write(json_encode([
                "type" => 'error',
                "error" => 400,
                "msg" => "$uri : URI mal formée dans la requete"
            ]));

            // Logger l'erreur
            // ->get : autre syntax d'accès au container
            $container->get('logger.erreor')->error("GET $uri : malformed uri");

            return $resp;
        };
    },

];
