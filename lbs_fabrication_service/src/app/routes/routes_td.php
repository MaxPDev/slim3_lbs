<?php

use DavidePastore\Slim\Validation\Validation;
use lbs\fab\app\controller\Commande_Controller;
use lbs\fab\app\controller\Commande_Item_Controller;
use lbs\fab\app\middleware\CommandeValidator;
use lbs\fab\app\middleware\Token;

// // Route test
// $app->get('/test/', Commande_Controller::class . ':test')
//     ->setName('test');


// Route pour une commande
$app->get('/td/commandes/{id}[/]', Commande_Controller::class . ':getCommande')
    ->setName('getCommande')
    ->add(Token::class . ':check');

// Route pour toute les commandes
$app->get('/td/commandes[/]', Commande_Controller::class . ':getAllCommande')
    ->setName('getAllCommande');


