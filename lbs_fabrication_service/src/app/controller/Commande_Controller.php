<?php

namespace lbs\fab\app\controller;

use DateTime;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\fab\app\errors\Writer;
use lbs\fab\app\models\Commande;
use lbs\fab\app\models\Item;
use \Slim\Container;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class Commande_Controller
{

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }


    // Toutes les commandes
    public function getAllCommande(Request $req, Response $resp): Response
    {
        //? réassembler tous les param dans une variable ou pas la peine ?

        // Variable pour pagination
        $size = 10; // 10 par défaut
        $page = 1; // page par défaut
        $nb_page_max = 0; // nombre de page

        // Récupération  du paramètre size si existant
        if (isset($req->getQueryParams()['size']) && is_numeric($req->getQueryParams()['size']) && $req->getQueryParams()['size'] > 0) {
            $size = intval($req->getQueryParams()['size']);
            $page = 1;
        }

        // Récupération du paramètre pagination si existant
        if (isset($req->getQueryParams()['page']) != null && is_numeric($req->getQueryParams()['page']) && $req->getQueryParams()['page'] > 0) {
            $page = intval($req->getQueryParams()['page']);
        } else if ($req->getQueryParams()['page'] < 0) {
            //TODO: error 
        }



        // Récupérer les commandes depuis le model
        $commandes = Commande::select(['id', 'nom', 'created_at', 'livraison', 'status'])->get();

        // Traitement du filtrage status
        if (isset($req->getQueryParams()['s']) && is_numeric($req->getQueryParams()['s'])) {
            $commandes = $commandes->where('status', intval($req->getQueryParams()['s']));
        }

        // Nombre total de commande après filtrage
        $nb_commandes = count($commandes);

        // Nombre de pages possibles : on divise le nombre de commande par le nombre d'éléments demandés par page
        $nb_page_max = intval($nb_commandes / $size);

        // Si la page demandé est supérieur au nombre de page possible, la dernière page possible est retournée.
        if ($page > $nb_page_max) {
            $page = $nb_page_max;
        }

        // Next page
        if ($page > 1) {
            if ($page < $nb_page_max) {
                $nextPage = $page + 1;
            } else {
                $nextPage = $nb_page_max;
            }
        } else {
            $nextPage = 2;
        }

        // Prev page
        if ($page > 1) {
            $prevPage = $page - 1;
        } else {
            $prevPage = 1;
        }

        // valueur du offset. -1 car l'index des commande commence à 0 et non à 1.
        $offset = ($page - 1) * $size;

        // Application  du nombre d'élément (size) et de la page demand" (offset)
        $commandes = $commandes->skip($offset)->take($size);

        $commandes_with_link = [];
        foreach ($commandes as $commande) {
            $commandes_with_link[] = [
                "command" => $commande,
                "links" => [
                    "self" => [
                        "href" => $this->container->router->pathFor('getCommande', ['id' => $commande->id]),
                        'next' => ['href' => $this->container->router->pathFor('getAllCommande', [], ['page' => $nextPage, 'size' => $size])],
                        'prev' => ['href' => $this->container->router->pathFor('getAllCommande', [], ['page' => $prevPage, 'size' => $size])],
                        'first' => ['href' => $this->container->router->pathFor('getAllCommande', [], ['page' => 1, 'size' => $size])],
                        'last' => ['href' => $this->container->router->pathFor('getAllCommande', [], ['page' => $nb_page_max, 'size' => $size])],
                    ]
                ]
            ];
        }
        // Construction des donnés à retourner dans le body
        $datas_resp = [
            "type" => "collection",
            // "count" => count($datas['commandes']),
            "count" => $nb_commandes,
            "page" => $page,
            "size" => $size,
            "commands" => $commandes_with_link
        ];

        $resp = $resp->withStatus(200);
        $resp = $resp->withHeader('application-header', 'TD 6');
        $resp = $resp->withHeader("Content-Type", "application/json;charset=utf-8");

        $resp->getBody()->write(json_encode($datas_resp));

        return $resp;
    }


}
