<?php

use lbs\command\app\controller\Demo_Controller;
use lbs\command\app\middleware\ExampleMiddleware;
use \Psr\Http\Message\ServerRequestInterface as Request ;
use \Psr\Http\Message\ResponseInterface as Response ;

//* Toutes les routes suivante sont seulement des tests et des notes d'apprentissage.

// td1

$app->get('/commandes/{id}',
    function (Request $req, Response $resp, array $args) {

        $id_commande = $args['id'];

        $datas = [
            'commandes' =>  [
                [
                    "id" => "truc1",
                    "mail_client" => "a@a.a",
                    "date_commande" => "2022-01-05 12:00:23",
                    "date_livraison" => "2022-01-05 13:00:23",
                    "montant" => 25.96
                ],
                [
                    "id" => "truc2",
                    "mail_client" => "b@b.b",
                    "date_commande" => "2022-01-05 14:00:23",
                    "date_livraison" => "2022-01-05 15:00:23",
                    "montant" => 21.76
                ],
                [
                    "id" => "truc3",
                    "mail_client" => "c@c.c",
                    "date_commande" => "2022-01-07 18:00:23",
                    "date_livraison" => "2022-01-08 13:00:23",
                    "montant" => 2.96
                ],
            ]
            ];

        $resp = $resp->withStatus(200);
        $resp = $resp->withHeader('application-header', 'TD 1');
        $resp = $resp->withHeader("Content-Type", "application/json;charset=utf-8");

        foreach ($datas['commandes'] as $commande) {
            if ($commande['id'] == $id_commande) {
                $commande_resp = $commande;
            };
        }

        $datas_resp = [
            "type" => "ressource",
            "commande" => $commande_resp
        ];

        $resp->getBody()->write(json_encode($datas_resp));

        return $resp;
    }
);


$app->get('/commandes[/]',
    function (Request $req, Response $resp) {
        $datas = [
            'commandes' =>  [
                [
                    "id" => "truc1",
                    "mail_client" => "a@a.a",
                    "date_commande" => "2022-01-05 12:00:23",
                    "montant" => 25.96
                ],
                [
                    "id" => "truc2",
                    "mail_client" => "b@b.b",
                    "date_commande" => "2022-01-05 14:00:23",
                    "montant" => 21.76
                ],
                [
                    "id" => "truc3",
                    "mail_client" => "c@c.c",
                    "date_commande" => "2022-01-07 18:00:23",
                    "montant" => 2.96
                ],
            ]
            ];

        $resp = $resp->withStatus(200);
        $resp = $resp->withHeader('application-header', 'TD 1');
        $resp = $resp->withHeader("Content-Type", "application/json;charset=utf-8");

        $datas_resp = [
            "type" => "collection",
            "count" => count($datas['commandes']),
            "commandes" => $datas['commandes']
        ];

        $resp->getBody()->write(json_encode($datas_resp));

        return $resp;
    }
);

// vidéo 1

$app->get('/huba/{name}[/]',
    function (Request $req, Response $resp, $args) {
        $name = $args['name'];
        $resp->getBody()->write("<h1>huba huba, $name</h1>");
        return $resp;
    }
);

// Mettre plus spécifique en 1er
$app->get('/hello/jb',
    function (Request $req, Response $resp, $args) {
        $resp->getBody()->write("<h1>Hello JB</h1>");
        return $resp;
    }
);

$app->get('/v2/hello_v2/{name}[/]',
    function (Request $req, Response $resp, $args) {
        $name = $args['name'];
        $resp->getBody()->write("<h1>Hello world, $name</h1>");
        return $resp;
    }
)->setName('hello');

// video 2

$app->post('/ciao/{name}[/]',
    function (Request $rq, Response $rs, array $args): Response {
        $data['args']        = $args['name'];
        $data['method']      = $rq->getMethod();
        $data['accept']      = $rq->getHeader('Accept');
        $data['query param'] = $rq->getQueryParam('p','no p');

        $data['content-type']= $rq->getContentType();
        // slim décode automatique le contenu en fonction de son type avec getParsedBody (json, from, text...)
        $data['body']        = $rq->getParsedBody();

        // PSR7 : objet non modifiable, non créer des nvx objets.
        // Par contre : body modifiable ! Donc on appelle la commande
        $rs = $rs->withStatus(202);
        $rs = $rs->withHeader('application-header', 'some value');

        $rs = $rs->withHeader("Content-Type", "application/json");

        // syntax possible, puis la méthode renvoie à chaque fois le résultat :
        // $rs = $rs->withStatus(202)->withHeader('application-header', 'some value')->withHeader("Content-Type", "application/json");



        $rs->getBody()->write(json_encode($data));
        return $rs;
    }
);


    // Video 3
    // pour rendre disponible le contenur : closure binding :
    // la fonction anonyme (= closure) est lié au contenu par $this. $this <=> $container
    $app->get('/video3/{name}[/]',
        function(Request $rq, Response $rs, array $args) : Response {
            $name = $args['name'];

            // $dbconf = $this['dbconf'];
            // soit on y accède par tableau['valeur'], ou par notation d'objet :
            $dbconf = $this->settings['dbconf'];

            $rs->getBody()->write("<h1>Hello $name, </h1> <h2>$dbconf</h2>");
            return $rs;
        }
);

// video 4 controller

// syntax 1

$app->get('/video4/{name}[/]',
    function(Request $rq, Response $rs, array $args) : Response {

        // On injecte le containeur de dépendance dans le controlleur
        $controleur = new Demo_Controller($this);
        return $controleur->sayHello($rq,$rs,$args);

    //     $p = $rq->getQueryParam('p', 0);
    //     $name = $args['name'];
    //     $dbconf = $this->settings['dbconf'];

    //     $rs->getBody()->write("<h1>Hello, $name</h1><h2>$dbconf : $dbconf</h2><h2>p = $p</h2>");
    //     return $rs;
    // 
    }
);

// syntax 2

// on a acchès au paramètre de configuration (containeur),sans passer explicitement $this
// Injection de dépendant auto
$app->get('/video4_2/{name}[/]', 'lbs\command\app\controller\Demo_Controller:sayHello');


// syntax 3 (slim only)
// Autocomplétion (php storm), auto use
$app->get('/video4_3/{name}', Demo_Controller::class . ':sayHello');


// Video 5
$app->get('/welcome/{name}[/]', Demo_Controller::class . ':welcome');


// Video 6
// !! Les services dans le conteneur  ne sont instancié qu'une seul fois !

$app->get('/video6[/]', function(Request $rq, Response $rs, array $args) : Response {
    $host = $this->dbhost;

    // On peut changer le service de markdown->html dans la conf
    // Ici pas de changement
    $m2html = $this->md2html;
    // $test = $this->test ;

    $rs->getBody()->write($m2html("# titre MD : $host ##"));
    
    // ! Configuration changé au cours du tuto ne fonctionnera plus, cf route suivante
    // * Syntax alternative: $this->get('logger')
    $this->logger->debug('GET /video6 pour voir le log');
    $this->logger->warning('GET / : warning, \'tention là');
    return $rs;
});

$app->post('/video7[/]', function(Request $rq, Response $rs, array $args) : Response {

    // test erreur :
    
    $uri = $rq->getUri();

    $this->get('logger.debug')->debug("$uri pour voir le log");
    $this->get('logger.warn')->warning("$uri : warning, \'tention là");
    $this->get('logger.error')->error("$uri Error");

    return $rs;
});

$app->get('/video7_1', Demo_Controller::class . ':test_error')->setName('video7_1');

$app->get('/uuid_test', Demo_Controller::class . ':uuid_test')->setName('uuid_test');

$app->get('/token_test', Demo_Controller::class . ':token_test')->setName('token_test');


// Video 9 Middleware
$app->get('/middletest/welcome[/]', Demo_Controller::class . ':welcome')->add(ExampleMiddleware::class . ':mw1')
                                                                        ->add(ExampleMiddleware::class . ':mw2');

$app->get('/middletest/{name}[/]', Demo_Controller::class . ':sayHello')->setName('middletest')
                                                                        ->add(ExampleMiddleware::class . ':mw1')
                                                                        ->add(ExampleMiddleware::class . ':mw3')
                                                                        ;




// $app->get('/hello/{name}',
//     function (Request $req, Response $resp, $args) {
//         $name = $args['name'];
//         $resp->getBody()->write(json_encode("Hello, $name"));
//         return $resp;
//     }
// )->setName('hello');

// $url = $app->getContainer()['router']->pathFor('hello', [ 'name'=>'bob']);

// $app->get('/hi/{name}',
//     function (Request $req, Response $resp, $args) {
//         $name = $args['name'];
//         $url = $this['router']->pathFor('hello', ['name' => 'bob']);
//         $resp->getBody()->write(json_encode("Hi, $name, $url"));
//         return $resp;
//     }
// )->setName('hi');

$app->get('/',
    function (Request $req, Response $resp) {
        $resp->getBody()->write(
            "<h1>Le Bon Sandwich...</h1><br>
            <h2>... is not ready yet</h2<br>
            <h3>Come back later</h3>");
        return $resp;
    }
);
