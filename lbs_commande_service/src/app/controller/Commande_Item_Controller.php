<?php

namespace lbs\command\app\controller;

use DateTime;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\command\app\errors\Writer;
use lbs\command\app\models\Commande;
use lbs\command\app\models\Item;
use \Slim\Container;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class Commande_Item_Controller
{

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    //* TD4.1 assiociations
    //  $app->get('/td/commandes/{id}/items]
    public function getItems(Request $req, Response $resp, array $args): Response
    {
        $id_commande = $args['id'];;
        $commandes = Commande::findOrFail($id_commande);
        $count_items = count($commandes->items);
        $items =  $commandes->items()->select('id', 'libelle', 'tarif', 'quantite')->get();


        $datas_resp = [
            "type" => "colletion",
            "count" => $count_items,
            "items" => $items,

        ];

        $resp = Writer::json_output($resp, 200);
        $resp = $resp->withHeader('application-header', 'TD 1');


        $resp->getBody()->write(json_encode($datas_resp));

        return $resp;
    }
}
