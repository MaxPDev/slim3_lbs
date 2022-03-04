<?php

namespace lbs\command\app\middleware;

use \Slim\Container;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use lbs\command\app\models\Commande;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\command\app\errors\Writer;

class Middleware
{

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function checkToken(Request $req, Response $resp, callable $next): Response
    {

        $token = null;

        // Vérifie la présence du token dans l'uri
        $token = $req->getQueryParam('token', null);

        // Si le token n'est pas dans l'uri, vérifie la présence du token dans le header 
        if (is_null($token)) {
            $api_header = $req->getHeader('X-commande_api-token');
            $token = $api_header[0] ?? null; // null coalesce plutôt que ternary operation
            // $token = (isset($api_header[0]) ? $api_header[0] : null);
        }
        if (empty($token)) {
            ($this->container->get('logger.error'))->error('No token in commande Route', [403]);
            return Writer::json_error($resp, 403, "No token");
        }
        $command_id = $req->getAttribute('route')->getArgument('id');

        $command = null;
        try {
            $command = Commande::where('id', '=', $command_id)->firstorFail();

            if ($command->token !== $token) {
                ($this->container->get('logger.error'))->error('Token doesnt match', [403]);
                return Writer::json_error($resp, 403, "Invalid token ! : $token");
            }
        } catch (ModelNotFoundException $e) {
            ($this->container->get('logger.error'))->error('Commande unknow', [404]);
            return Writer::json_error($resp, 404, "Invalid token ! : $token");
        }

        // On passe la commande au middleware d'après : éviter de refaire la requête
        $req = $req->withAttribute('commande', $command);
        $resp = $next($req, $resp);

        return $resp;
    }
}

    // //todo faire un middleware token avec une methode check
    // $token = null
    // $token = rq get queryparal
    // if is_null $token
    // $api_header = rq get header X-commande_api-token
    // $token isset 
