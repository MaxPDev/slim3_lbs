<?php

use lbs\command\app\controller\Commande_Controller;
use lbs\command\app\controller\Commande_Item_Controller;

//* TD1 & TD2

// Route pour une commande
$app->get('/td/commandes/{id}[/]', Commande_Controller::class . ':getCommande')->setName('getCommande');

// Route pour toute les commandes
$app->get('/td/commandes[/]', Commande_Controller::class . ':getAllCommande')->setName('getAllCommande');


//* TD3

$app->put('/td/commandes/{id}/items[/]', Commande_Controller::class . ':putCommande')->setName('putCommande');


//* TD4

$app->get('/td/commandes/{id}/items[/]', Commande_Item_Controller::class . ':getItems')->setName('getCommandesItems');

//* TD5

// Création d'une commande
$app->post('/td/commandes[/]', Commande_Controller::class . ':createCommande')->setName('createCommande');
