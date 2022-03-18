<?php

use DavidePastore\Slim\Validation\Validation;
use lbs\auth\app\controller\LBSAuthController;
use lbs\auth\app\middleware\Token;

//* TD7
// $app->get('/td/test[/]', LBSAuthController::class . ':test')
//     ->setName('test');


$app->post('/auth[/]', LBSAuthController::class . ':authenticate')
    ->setName('authentification');


$app->get('/check[/]', LBSAuthController::class . ':check')
    ->setName('check');
