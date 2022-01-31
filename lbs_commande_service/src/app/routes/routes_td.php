<?php

use lbs\command\app\controller\TD123Controller;

//* TD1 & TD2

// Route pour une commande
$app->get('/td/commandes/{id}[/]', TD123Controller::class . ':getCommande')->setName('getCommande');

// Route pour toute les commandes
$app->get('/td/commandes[/]', TD123Controller::class . ':getAllCommande')->setName('getAllCommande');

//* TD3

$app->put('/td/commandes/{id}[/]', TD123Controller::class . ':putCommande')->setName('putCommande');
