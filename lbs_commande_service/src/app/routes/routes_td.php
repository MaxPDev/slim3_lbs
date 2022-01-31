<?php

use lbs\command\app\controller\TD12Controller;

//* TD1

// Route pour une commande
$app->get('/td1/commandes/{id}[/]', TD12Controller::class . ':getCommande')->setName('commande');

// Route pour toute les commandes
$app->get('/td1/commandes[/]', TD12Controller::class . ':getAllCommande')->setName('commandes');
