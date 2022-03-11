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

    // // Créer une commande
    // public function createCommande(Request $req, Response $resp, array $args): Response
    // {

    //     // Controle de donné à faire plus tard  (middleware respect/validation : davidepastrore/slim-validation)

    //     // Flitrer données pour éviter injection (on suppose qu'elle sont présentes et complète //? a coder plus tard ?)
    //     // On ne traitre pas non plus la liste des items commander. Montant de commande : 0 //? à faire plus tard ? 


    //     //TODO: - données transmises en json
    //     //TODO: - ID d'une commande : uuid
    //     //TODO: - Création d'une nvlle commande => génération d'un token unique, cryptographique, retourné dans la rep
    //     //TODO: et utilisé pour valider les prochaines requête de cette même commande
    //     //TODO: Remplace FILTER_SANITIZE_STRING par htmlentities, ou htmlspecialchars (check param) ou strip_tags.
    //     //? check_Token : middleware, mais createToken-> middleware ??

    //     // Récupération du body de la requête
    //     $commande_req = $req->getParsedBody();

    //     if ($req->getAttribute('has_errors')) {

    //         $errors = $req->getAttribute('errors');

    //         //? à mettre ailleurs ? Container ? Utils ? Maiddleware ? Errors ? Faire fonction + générique
    //         if (isset($errors['nom'])) {
    //             $this->container->get('logger.error')->error("error input nom client");
    //             return Writer::json_error($resp,403, '"nom" : invalid input, String expected');
    //         }
    //         if (isset($errors['mail'])) {
    //             $this->container->get('logger.error')->error("error mail input client");
    //             return Writer::json_error($resp,403, '"mail" : invalid input, email format expected');
    //         }
    //         if (isset($errors['livraison.date'])) {    
    //             $this->container->get('logger.error')->error("error input livraison date");
    //             return Writer::json_error($resp,403, '"date" : invalid input. d-m-Y format expected, today or later');
    //         }
    //         if (isset($errors['livraison.heure'])) {    
    //             $this->container->get('logger.error')->error("error input livraison heure");
    //             return Writer::json_error($resp,403, '"heure" : invalid input. H:i format expected');
    //         }
    //         if (isset($errors['items.uri'])) {
    //             ($this->container->get('logger.error'))->error("error input uri");
    //             return Writer::json_error($resp,403, '"uri" : invalid input. String expected');
    //         }
    //         if (isset($errors['items.q'])) {
    //             ($this->container->get('logger.error'))->error("error input quantite");
    //             return Writer::json_error($resp,403, '"q" : invalid input. integer expected');
    //         }
    //         if (isset($errors['items.libelle'])) {
    //             ($this->container->get('logger.error'))->error("error input lebelle");
    //             return Writer::json_error($resp,403, '"libelle" : String expected');
    //         }
    //         if (isset($errors['items.tarif'])) {
    //             ($this->container->get('logger.error'))->error("error input tarif");
    //             return Writer::json_error($resp,403, '"tarif" : float expected');
    //         }
    //     };


    //     //! Mettre les if isset etc.... mettre pour mail : || !filter_var($command_req['mail_client ...])

    //     try {

    //         // Récupération de la fonction UUID generator depuis le container
    //         $new_uuid = $this->container->uuid;

    //         //Récupération de la fonction token depuis le container
    //         $new_token = $this->container->token;

    //         // Création d'une commande via le model
    //         $new_commande = new Commande();

    //         $new_commande->nom = filter_var($commande_req['nom'], FILTER_SANITIZE_STRING);
    //         $new_commande->mail = filter_var($commande_req['mail'], FILTER_SANITIZE_EMAIL);

    //         // Création de la date  de livraison
    //         $date_livraison = new DateTime($commande_req['livraison']['date'] . ' ' . $commande_req['livraison']['heure']);
    //         $new_commande->livraison = $date_livraison->format('Y-m-d H:i:s');

    //         // $new_commande->status = Commande::CREATED; ??

    //         // génération id basé sur un aléa : UUID v4
    //         $new_commande->id = $new_uuid(4);

    //         // Génération token
    //         $new_commande->token = $new_token(32);

    //         $new_commande->montant = 0;

    //         // Récupération des items de la requête
    //         $items_req = $commande_req['items'];
    //         //TODO: Filtrate items ?
    //         foreach ($items_req as $item_req) {
    //             $new_item = new Item();
    //             $new_item->uri = $item_req['uri'];
    //             $new_item->quantite = $item_req['q'];
    //             $new_item->libelle = $item_req['libelle'];
    //             $new_item->tarif = $item_req['tarif'];
    //             $new_item->command_id = $new_commande->id;
    //             $new_commande->montant += $item_req['tarif'];
    //             $new_item->save();
    //         }

    //         $new_commande->save();

    //         // Récupération du path pour le location dans header
    //         $pathForCommandes = $this->container->router->pathFor(
    //             'getCommande',
    //             ['id' => $new_commande->id]
    //         );

    //         $datas_resp = [
    //             "type" => "ressource",
    //             "commande" => [
    //                 "nom" => $new_commande->nom,
    //                 "mail" => $new_commande->mail,
    //                 "date_livraison" => $date_livraison->format('Y-m-d H:m'),
    //                 "id" => $new_commande->id,
    //                 "token" => $new_commande->token,
    //                 "montant" => $new_commande->montant
    //             ]
    //         ];

    //         $resp = Writer::json_output($resp, 201)
    //             ->withAddedHeader('application-header', 'TD 5') // 201 : created
    //             ->withHeader("Location", $pathForCommandes);

    //         $resp->getBody()->write(json_encode($datas_resp));

    //         return $resp;
    //     } catch (ModelNotFoundException $e) {
    //         //todo: logError
    //         return Writer::json_error($resp, 404, 'Ressource not found : command ID = ' . $new_commande->id);
    //     } catch (\Exception $th) {
    //         //todo : log Error
    //         return Writer::json_error($resp, 500, 'Server Error : Can\'t create command');
    //     }
    //     //
    // }

    // // get une commande
    // public function getCommande(Request $req, Response $resp, array $args): Response
    // {
    //     $id_commande = $args['id'];

    //     // Récupérer les queries
    //     $query_embed = $req->getQueryParams()['embed'] ?? null;

    //     try {

    //         // $commande = Commande::select(['id', 'nom', 'mail', 'montant'])
    //         //                     ->where('id', '=', $id_commande)
    //         //                     ->firstOrFail();

    //         //* Modification TD4.2
    //         $commande = Commande::select(['id', 'mail', 'nom', 'created_at', 'updated_at', 'livraison', 'montant'])
    //             ->where('id', '=', $id_commande)
    //             ->firstOrFail();

    //         // Récupération de la route commandes                            
    //         $pathForCommandes = $this->container->router->pathFor(
    //             'getCommande',
    //             ['id' => $id_commande]
    //         );

    //         // Récupération de la rouge commandesItems
    //         // Récupération de la route                                
    //         $pathForCommandesItems = $this->container->router->pathFor(
    //             'getCommandesItems',
    //             ['id' => $id_commande]
    //         );

    //         // Création des liens hateos
    //         $hateoas = [
    //             "items" => ["href" => $pathForCommandesItems],
    //             "self" => ["href" => $pathForCommandes]
    //         ];


    //         // Création du body de la réponse
    //         //? Renomer les keys ou laisser les noms issus de la DB ?
    //         $datas_resp = [
    //             "type" => "ressource",
    //             // "commande" => $commande_resp
    //             "commande" => $commande,
    //             "links" => $hateoas
    //         ];

    //         // Ressources imbiriquée //? peut se mettre/s'automatiser ailleurs ?
    //         if ($query_embed === 'categories') { //? invoquer directmeent getQueryParam ici ? 
    //             //! === items plutôt ?? $query=$query->with('items') ??? faire une seul query pour tout, mettre dans try catch
    //             $items = $commande->items()->select('id', 'libelle', 'tarif', 'quantite')->get();
    //             $datas_resp["commande"]["items"] = $items;
    //         }


    //         $resp = $resp->withStatus(200);
    //         $resp = $resp->withHeader('application-header', 'TD 1');
    //         $resp = $resp->withHeader("Content-Type", "application/json;charset=utf-8");


    //         $resp->getBody()->write(json_encode($datas_resp));

    //         return $resp;
    //     } catch (ModelNotFoundException $e) {

    //         //TODO: Ask
    //         //? Which is the best ??

    //         $clientError = $this->container->clientError;
    //         return $clientError($req, $resp, 404, "Commande not found");


    //         // return Writer::json_error($resp, 404, "Alors j'ai bien regardé, j'ai pas trouvé ta commande");
    //     }
    // }

    // Toutes les commandes
    public function getAllCommande(Request $req, Response $resp): Response
    {
        //? réassembler tous les param dans une variable ou pas la peine ?

        // Variable pour pagination
        $size = 10; // 10 par défaut
        $page = 0; // page demandé
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

    // // Remplacer une commande. PUT, pas PATCH !!
    // public function putCommande(Request $req, Response $resp, array $args): Response
    // {

    //     $commande_data = $req->getParsedBody();

    //     $clientError = $this->container->clientError;

    //     if (!isset($commande_data['nom_client'])) {
    //         return $clientError($req, $resp, 400, "Missing 'nom_client");
    //         // return Writer::json_error($resp, 400, "missing 'nom_client'");
    //     };

    //     if (!isset($commande_data['mail_client'])) {
    //         return Writer::json_error($resp, 400, "missing 'mail_client'");
    //     };

    //     if (!isset($commande_data['livraison']['date'])) {
    //         return Writer::json_error($resp, 400, "missing 'livraison(date)'");
    //     };

    //     if (!isset($commande_data['livraison']['heure'])) {
    //         return Writer::json_error($resp, 400, "missing 'livraison(heure)'");
    //     };

    //     try {
    //         // Récupérer la commande
    //         $commande = Commande::Select(['id', 'nom', 'mail', 'livraison'])->findOrFail($args['id']);

    //         $commande->nom = filter_var($commande_data['nom_client'], FILTER_SANITIZE_STRING);
    //         $commande->mail = filter_var($commande_data['mail_client'], FILTER_SANITIZE_EMAIL);
    //         $commande->livraison = DateTime::createFromFormat(
    //             'Y-m-d H:i',
    //             $commande_data['livraison']['date'] . ' ' .
    //                 $commande_data['livraison']['heure']
    //         );

    //         $commande->save();

    //         return Writer::json_output($resp, 204);
    //     } catch (ModelNotFoundException $e) {
    //         return Writer::json_error($resp, 404, "commande inconnue : {$args}");
    //     } catch (\Exception $e) {
    //         return Writer::json_error($resp, 500, $e->getMessage());
    //     }






    // return $resp;
    // }


    //     // Toutes les commandes
    //     public function test(Request $req, Response $resp): Response
    //     {

    //         // Construction des donnés à retourner dans le body
    //         $datas_resp = [
    //             "test" => "test",
    //         ];

    //         $resp = $resp->withStatus(200);
    //         $resp = $resp->withHeader('application-header', 'TD 6');
    //         $resp = $resp->withHeader("Content-Type", "application/json;charset=utf-8");

    //         $resp->getBody()->write(json_encode($datas_resp));

    //         return $resp;
    //     }
}
