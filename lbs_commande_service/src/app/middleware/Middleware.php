<?php

namespace lbs\command\app\middleware;

use \Slim\Container;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use lbs\command\app\models\Commande;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Middleware
{

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function checkToken(Request $req, Response $resp, callable $next): Response
    {

        // récupérer l'identifiant de cmmde dans la route et le token
        $id = $req->getAttribute('route')->getArgument('id');
        $token = $req->getQueryParam('token', null);
        // vérifier que le token correspond à la commande
        try {
            Commande::where('id', '=', $id)
                ->where('token', '=', $token)
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            // générer une erreur
            return $resp;
        };

        return $resp;
    }
}
