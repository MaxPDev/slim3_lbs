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
    public function createCommande(Request $req, Response $resp, array $args): Response
    {

        // Controle de donné à faire plus tard  (middleware respect/validation : davidepastrore/slim-validation)

        // Flitrer données pour éviter injection (on suppose qu'elle sont présentes et complète //? a coder plus tard ?)
        // On ne traitre pas non plus la liste des items commander. Montant de commande : 0 //? à faire plus tard ? 


        //TODO: - données transmises en json
        //TODO: - ID d'une commande : uuid
        //TODO: - Création d'une nvlle commande => génération d'un token unique, cryptographique, retourné dans la rep
        //TODO: et utilisé pour valider les prochaines requête de cette même commande
        //TODO: Remplace FILTER_SANITIZE_STRING par htmlentities, ou htmlspecialchars (check param) ou strip_tags.
        //? check_Token : middleware, mais createToken-> middleware ??

        // Récupération du body de la requête
        $commande_creation_req = $req->getParsedBody();

        //TODO: Try Catch ici
        // Création d'une commande via le model
        $new_commande = new Commande();

        $new_commande->nom = filter_var($commande_creation_req['nom'], FILTER_SANITIZE_STRING);
        $new_commande->mail = filter_var($commande_creation_req['mail'], FILTER_SANITIZE_EMAIL);
        $new_commande->livraison = DateTime::createFromFormat( //TODO: Date doesn't work
            'Y-m-d H:i',
            $commande_creation_req['livraison']['date'] . ' ' .
                $commande_creation_req['livraison']['heure']
        );

        // Récupération de la fonction UUID generator depuis le container
        $new_uuid = $this->container->uuid;

        //Récupération de la fonction token depuis le container
        $new_token = $this->container->token;

        // génération id basé sur un aléa : UUID v4
        $new_commande->id = $new_uuid(4);

        // Génération token
        $new_commande->token = $new_token(32);
        $new_commande->montant = 0;

        $new_commande->save();

        // Récupération du path pour le location dans header
        $pathForCommandes = $this->container->router->pathFor(
            'getCommande',
            ['id' => $new_commande->id]
        );

        $datas_resp = [
            "commande" => [
                "nom" => $new_commande->nom,
                "mail" => $new_commande->mail,
                "date_livraison" => $new_commande->livraison->format('Y-m-d H:i'),
                "id" => $new_commande->id,
                "token" => $new_commande->token,
                "montant" => $new_commande->montant
            ]
        ];

        $resp = $resp->withStatus(201); // 201 : created
        $resp = $resp->withHeader('application-header', 'TD 5');
        $resp = $resp->withHeader("Content-Type", "application/json;charset=utf-8");
        $resp = $resp->withHeader("Location", $pathForCommandes);

        //TODO: Location ?

        $resp->getBody()->write(json_encode($datas_resp));

        return $resp;
    }

    // get une commande
    public function getCommande(Request $req, Response $resp, array $args): Response
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
            $pathForCommandes = $this->container->router->pathFor(
                'getCommande',
                ['id' => $id_commande]
            );

            // Création des liens hateos
            //TODO: lien item à modifier avec path spécifique à commandes/{id}/items
            $hateoas = [
                "items" => ["href" => $pathForCommandes . 'items'],
                "self" => ["href" => $pathForCommandes]
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
            if ($queries['embed'] === 'categories') { //? invoquer directmeent getQueryParam ici ?
                $items = $commande->items()->select('id', 'libelle', 'tarif', 'quantite')->get();
                $datas_resp["commande"]["items"] = $items;
            }


            $resp = $resp->withStatus(200);
            $resp = $resp->withHeader('application-header', 'TD 1');
            $resp = $resp->withHeader("Content-Type", "application/json;charset=utf-8");


            $resp->getBody()->write(json_encode($datas_resp));

            return $resp;
        } catch (ModelNotFoundException $e) {

            //TODO: Ask
            //? Which is the best ??

            $clientError = $this->container->clientError;
            return $clientError($req, $resp, 404, "Commande not found");


            // return Writer::json_error($resp, 404, "Alors j'ai bien regardé, j'ai pas trouvé ta commande");
        }
    }

    // Toutes les commandes
    public function getAllCommande(Request $req, Response $resp): Response
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
    public function putCommande(Request $req, Response $resp, array $args): Response
    {

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
            $commande->livraison = DateTime::createFromFormat(
                'Y-m-d H:i',
                $commande_data['livraison']['date'] . ' ' .
                    $commande_data['livraison']['heure']
            );

            $commande->save();

            return Writer::json_output($resp, 204);
        } catch (ModelNotFoundException $e) {
            return Writer::json_error($resp, 404, "commande inconnue : {$args}");
        } catch (\Exception $e) {
            return Writer::json_error($resp, 500, $e->getMessage());
        }






        return $resp;
    }
}
