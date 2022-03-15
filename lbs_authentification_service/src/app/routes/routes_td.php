<?php

use DavidePastore\Slim\Validation\Validation;
use lbs\auth\app\controller\LBSAuthController;
use lbs\auth\app\middleware\Token;

//* TD7
$app->get('/td/test[/]', LBSAuthController::class . ':test')
    ->setName('test');



// Route pour une commande
$app->get('/td/commandes/{id}[/]', Commande_Controller::class . ':getCommande')
    ->setName('getCommande')
    ->add(Token::class . ':check');

// // Route pour toute les commandes
// $app->get('/td/commandes[/]', Commande_Controller::class . ':getAllCommande')
//     ->setName('getAllCommande');


// //* TD3

// $app->put('/td/commandes/{id}/items[/]', Commande_Controller::class . ':putCommande')
//     ->setName('putCommande');


// //* TD4

// $app->get('/td/commandes/{id}/items[/]', Commande_Item_Controller::class . ':getItems')
//     ->setName('getCommandesItems');

// //! correctin : getCommandItem, class Token::class : check pour middleware

// //* TD5

// // Création d'une commande
// $app->post('/td/commandes[/]', Commande_Controller::class . ':createCommande')
//     ->add(new Validation(CommandeValidator::create_validators()))
//     ->setName('createCommande');


    //! token a mettre sur item, commande et paiement, et replacecommant (put)