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
        //todo try catch
        $commandes = Commande::findOrFail($id_commande); //!select(['id]) avant le find or fail
        $count_items = count($commandes->items);
        $items =  $commandes->items()->select('id', 'libelle', 'tarif', 'quantite')->get();


        $datas_resp = [
            "type" => "colletion",
            "count" => $count_items,
            "items" => $items,

        ];

        $resp = Writer::json_output($resp, 200); //todo  $resp,200, $data
        $resp = $resp->withHeader('application-header', 'TD 1');

        //todo catch modelnotfoundexception
        //todo this container get logger.error -> 404 not found.... + writer ?


        $resp->getBody()->write(json_encode($datas_resp));

        return $resp;
    }
}
