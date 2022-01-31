<?php

namespace lbs\command\app\controller;

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

    public function getCommande(Request $req, Response $resp, array $args)
    {
        $id_commande = $args['id'];

        // // Data pour le td1
        // $datas = [
        //     'commandes' =>  [
        //         [
        //             "id" => "truc1",
        //             "mail_client" => "a@a.a",
        //             "date_commande" => "2022-01-05 12:00:23",
        //             "date_livraison" => "2022-01-05 13:00:23",
        //             "montant" => 25.96
        //         ],
        //         [
        //             "id" => "truc2",
        //             "mail_client" => "b@b.b",
        //             "date_commande" => "2022-01-05 14:00:23",
        //             "date_livraison" => "2022-01-05 15:00:23",
        //             "montant" => 21.76
        //         ],
        //         [
        //             "id" => "truc3",
        //             "mail_client" => "c@c.c",
        //             "date_commande" => "2022-01-07 18:00:23",
        //             "date_livraison" => "2022-01-08 13:00:23",
        //             "montant" => 2.96
        //         ],
        //     ]
        // ];

        $commande = Commande::select(['id', 'nom', 'mail', 'montant'])
                            ->firstOrFail($id_commande);

        $datas_resp = [
            "type" => "ressource",
            // "commande" => $commande_resp
            "commande" => $commande
        ];
        
        // foreach ($datas['commandes'] as $commande) {
        //     if ($commande['id'] == $id_commande) {
        //         $commande_resp = $commande;
        //     };
        // }

        $resp = $resp->withStatus(200);
        $resp = $resp->withHeader('application-header', 'TD 1');
        $resp = $resp->withHeader("Content-Type", "application/json;charset=utf-8");


        $resp->getBody()->write(json_encode($datas_resp));

        return $resp;
    }

    public function getAllCommande(Request $req, Response $resp)
    {

        // // Data pour le td1
        // $datas = [
        //     'commandes' =>  [
        //         [
        //             "id" => "truc1",
        //             "mail_client" => "a@a.a",
        //             "date_commande" => "2022-01-05 12:00:23",
        //             "montant" => 25.96
        //         ],
        //         [
        //             "id" => "truc2",
        //             "mail_client" => "b@b.b",
        //             "date_commande" => "2022-01-05 14:00:23",
        //             "montant" => 21.76
        //         ],
        //         [
        //             "id" => "truc3",
        //             "mail_client" => "c@c.c",
        //             "date_commande" => "2022-01-07 18:00:23",
        //             "montant" => 2.96
        //         ],
        //     ]
        // ];

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
