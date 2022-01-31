<?php

namespace lbs\command\app\controller;

use \Slim\Container;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class TD12Controller {

    private $container; 

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getCommande(Request $req, Response $resp, array $args) {
        $id_commande = $args['id'];

        $datas = [
            'commandes' =>  [
                [
                    "id" => "truc1",
                    "mail_client" => "a@a.a",
                    "date_commande" => "2022-01-05 12:00:23",
                    "date_livraison" => "2022-01-05 13:00:23",
                    "montant" => 25.96
                ],
                [
                    "id" => "truc2",
                    "mail_client" => "b@b.b",
                    "date_commande" => "2022-01-05 14:00:23",
                    "date_livraison" => "2022-01-05 15:00:23",
                    "montant" => 21.76
                ],
                [
                    "id" => "truc3",
                    "mail_client" => "c@c.c",
                    "date_commande" => "2022-01-07 18:00:23",
                    "date_livraison" => "2022-01-08 13:00:23",
                    "montant" => 2.96
                ],
            ]
            ];

        $resp = $resp->withStatus(200);
        $resp = $resp->withHeader('application-header', 'TD 1');
        $resp = $resp->withHeader("Content-Type", "application/json;charset=utf-8");

        foreach ($datas['commandes'] as $commande) {
            if ($commande['id'] == $id_commande) {
                $commande_resp = $commande;
            };
        }

        $datas_resp = [
            "type" => "ressource",
            "commande" => $commande_resp
        ];

        $resp->getBody()->write(json_encode($datas_resp));

        return $resp;
    }

    public function getAllCommande(Request $req, Response $resp) {
        $datas = [
            'commandes' =>  [
                [
                    "id" => "truc1",
                    "mail_client" => "a@a.a",
                    "date_commande" => "2022-01-05 12:00:23",
                    "montant" => 25.96
                ],
                [
                    "id" => "truc2",
                    "mail_client" => "b@b.b",
                    "date_commande" => "2022-01-05 14:00:23",
                    "montant" => 21.76
                ],
                [
                    "id" => "truc3",
                    "mail_client" => "c@c.c",
                    "date_commande" => "2022-01-07 18:00:23",
                    "montant" => 2.96
                ],
            ]
            ];

        $resp = $resp->withStatus(200);
        $resp = $resp->withHeader('application-header', 'TD 1');
        $resp = $resp->withHeader("Content-Type", "application/json;charset=utf-8");

        $datas_resp = [
            "type" => "collection",
            "count" => count($datas['commandes']),
            "commandes" => $datas['commandes']
        ];

        $resp->getBody()->write(json_encode($datas_resp));

        return $resp;
    }

}