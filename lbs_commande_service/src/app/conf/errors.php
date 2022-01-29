<?php

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

return [

    // 405 : bon url/i, mauvaise méthode http

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
        return function (Request $req, Response $resp) use ($container): Response {

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
            $container->get('logger.error')->error("GET $uri : malformed uri");

            return $resp;
        };
    }, // donc le handler permet d'avoir accet au contenur pour utiliser le logger, avec les paramètres définis dans settings. izi.

    // Method not allowed réécrit pour le cas d'une API :
    'notAllowedHandler' => function ($container) {

        //* $methods : Tableau des méthodes permise. La fonction la reçoit dans ce context.
        return function (Request $req, Response $resp, array $methods) use ($container): Response {

            $methods_expected = implode(', ', $methods);

            $method_received = $req->getMethod();
            $uri             = $req->getUri();

            $resp = $resp->withStatus(405) // Déjà 405 par défaut
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('Allow', implode(', ', $methods)) //* Header pour afficher méthodes autorisées.
                ->write(json_encode([
                    'type'    => 'error',
                    'error'   => 405,
                    'message' => "method $method_received not allowed for uri $uri. Waited : " .
                        $methods_expected
                ]));

                //* Logger dans tous les handlers d'erreur
            $container->get('logger.error')->error("$method_received $uri : bad method - $methods_expected wanted");

            return $resp;
        };
    },

    // erreur serveur, donc php, donc, type throwable
    'phpErrorHandler' => function ($container) {

        return function (Request $req, Response $resp, \Throwable $error) use ($container): Response {

            $resp = $resp->withStatus(500) //* Internal Server Error
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode([
                    'type'    => 'error',
                    'error'   => 500,  
                    'message' => "internal server error: {$error->getMessage()}", //* On récupère les msg de $error pour les afficher
                    'trace'   => $error->getTraceAsString(), 
                    "file"    => $error->getFile() . "line: " . $error->getLine()
                ]));

                //TODO: Code message log
            // $container->get('logger.error')->error("$method_received $uri : bad method - $methods_expected wanted");

            return $resp;
        };
    }, // l'erreur devient exploitable par le client API qui la reçoit.



];
