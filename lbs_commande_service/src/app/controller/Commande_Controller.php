<?php

namespace lbs\command\app\controller;

use DateTime;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\command\app\errors\Writer;
use lbs\command\app\models\Commande;
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

    // Créer une commande
    public function createCommande(Request $req, Response $resp, array $args) : Response {

        // Controle de donné à faire plus tard  (middleware respect/validation : davidepastrore/slim-validation)

        // Flitrer données pour éviter injection (on suppose qu'elle sont présentes et complète //? a coder plus tard ?)
        // On ne traitre pas non plus la liste des items commander. Montant de commande : 0 //? à faire plus tard ? 


        //TODO: - données transmises en json
        //TODO: - ID d'une commande : uuid
        //TODO: - Création d'une nvlle commande => génération d'un token unique, cryptographique, retourné dans la rep
        //TODO: et utilisé pour valider les prochaines requête de cette même commande

        $datas_resp = [
            "commande" => [
                "to" => "do"
            ]
        ];

        $resp = $resp->withStatus(201); // 201 : created
        $resp = $resp->withHeader('application-header', 'TD 5');
        $resp = $resp->withHeader("Content-Type", "application/json;charset=utf-8");
        //TODO: Location ?

        $resp->getBody()->write(json_encode($datas_resp));

        return $resp;
    }

    // get une commande
    public function getCommande(Request $req, Response $resp, array $args) : Response
    {
        $id_commande = $args['id'];

        // Récupérer les queries
        $queries = $req->getQueryParams() ?? null;

        try {

            // $commande = Commande::select(['id', 'nom', 'mail', 'montant'])
            //                     ->where('id', '=', $id_commande)
            //                     ->firstOrFail();
    
            //* Modification TD4.2
            $commande = Commande::select(['id', 'mail', 'nom', 'created_at', 'updated_at', 'livraison', 'montant'])
                                ->where('id', '=', $id_commande)
                                ->firstOrFail();

            // Récupération de la route                                
            $pathForCommandes = $this->container->router->pathFor('getCommande', 
                                                                  ['id' => $id_commande]);

            // Création des liens hateos
            //TODO: lien item à modifier avec path spécifique à commandes/{id}/items
            $hateoas = [
                "items" => [ "href" => $pathForCommandes . 'items' ],
                "self" => [ "href" => $pathForCommandes ]
            ];

            
            // Création du body de la réponse
            //? Renomer les keys ou laisser les noms issus de la DB ?
            $datas_resp = [
                "type" => "ressource",
                // "commande" => $commande_resp
                "commande" => $commande,
                "links" => $hateoas
            ];
            
            // Ressources imbiriquée //? peut se mettre/s'automatiser ailleurs ?
            if($queries['embed'] === 'categories') { //? invoquer directmeent getQueryParam ici ?
                $items = $commande->items()->select('id', 'libelle','tarif','quantite')->get();
                $datas_resp["commande"]["items"] = $items;
            } 
            
    
            $resp = $resp->withStatus(200);
            $resp = $resp->withHeader('application-header', 'TD 1');
            $resp = $resp->withHeader("Content-Type", "application/json;charset=utf-8");
    
    
            $resp->getBody()->write(json_encode($datas_resp));
    
            return $resp;
        }

        catch (ModelNotFoundException $e) {
            
            //TODO: Ask
            //? Which is the best ??
            
            $clientError = $this->container->clientError;
            return $clientError($req, $resp, 404, "Commande not found");


            // return Writer::json_error($resp, 404, "Alors j'ai bien regardé, j'ai pas trouvé ta commande");
        }
    }

    // Toutes les commandes
    public function getAllCommande(Request $req, Response $resp) : Response
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

    // Remplacer une commande. PUT, pas PATCH !!
    public function putCommande(Request $req, Response $resp, array $args) : Response {

        $commande_data = $req->getParsedBody();

        $clientError = $this->container->clientError;

        if (!isset($commande_data['nom_client'])) {
            return $clientError($req, $resp, 400, "Missing 'nom_client");
            // return Writer::json_error($resp, 400, "missing 'nom_client'");
        };

        if (!isset($commande_data['mail_client'])) {
            return Writer::json_error($resp, 400, "missing 'mail_client'");
        };

        if (!isset($commande_data['livraison']['date'])) {
            return Writer::json_error($resp, 400, "missing 'livraison(date)'");
        };

        if (!isset($commande_data['livraison']['heure'])) {
            return Writer::json_error($resp, 400, "missing 'livraison(heure)'");
        };

        try {
            // Récupérer la commande
            $commande = Commande::Select(['id', 'nom', 'mail', 'livraison'])->findOrFail($args['id']);

            $commande->nom = filter_var($commande_data['nom_client'], FILTER_SANITIZE_STRING);
            $commande->mail = filter_var($commande_data['mail_client'], FILTER_SANITIZE_EMAIL);
            $commande->livraison = DateTime::createFromFormat('Y-m-d H:i',
                                    $commande_data['livraison']['date'] . ' ' .
                                    $commande_data['livraison']['heure']);

            $commande->save();

            return Writer::json_output($resp, 204);
        }

        catch (ModelNotFoundException $e) {
            return Writer::json_error($resp, 404, "commande inconnue : {$args}");
        }

        catch (\Exception $e) {
            return Writer::json_error($resp, 500, $e->getMessage());
        }






        return $resp;

    }
}

