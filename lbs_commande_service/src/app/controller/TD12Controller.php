<?php

namespace lbs\command\app\controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\command\app\errors\Writer;
use lbs\command\app\models\Commande;
use \Slim\Container;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class TD12Controller
{

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    // get une commande
    public function getCommande(Request $req, Response $resp, array $args)
    {
        $id_commande = $args['id'];;

        try {

            $commande = Commande::select(['id', 'nom', 'mail', 'montant'])
                                ->where('id', '=', $id_commande)
                                ->firstOrFail();
    
            $datas_resp = [
                "type" => "ressource",
                // "commande" => $commande_resp
                "commande" => $commande
            ];
    
            $resp = $resp->withStatus(200);
            $resp = $resp->withHeader('application-header', 'TD 1');
            $resp = $resp->withHeader("Content-Type", "application/json;charset=utf-8");
    
    
            $resp->getBody()->write(json_encode($datas_resp));
    
            return $resp;
        }

        catch (ModelNotFoundException $e) {
            
            //? Which is the best ??
            
            $clientError = $this->container->clientError;
            return $clientError($req, $resp, $args, 404, "Commande not found");


            // return Writer::json_error($resp, 404, "Alors j'ai bien regardé, j'ai pas trouvé ta commande");
        }
    }

    // Toutes les commandes
    public function getAllCommande(Request $req, Response $resp)
    {

        // Récupérer les commandes depuis le model
        $commandes = Commande::select(['id', 'nom', 'mail', 'montant'])->get();

        // Construction des donnés à retourner dans le body
        $datas_resp = [
            "type" => "collection",
            // "count" => count($datas['commandes']),
            "count" => count($commandes),
            "commandes" => $commandes
        ];

        $resp = $resp->withStatus(200);
        $resp = $resp->withHeader('application-header', 'TD 1');
        $resp = $resp->withHeader("Content-Type", "application/json;charset=utf-8");

        $resp->getBody()->write(json_encode($datas_resp));

        return $resp;
    }
}
