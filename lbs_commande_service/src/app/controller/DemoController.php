<?php

namespace lbs\command\app\controller;

use \Slim\Container;
use Ramsey\Uuid\Uuid;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

class DemoController
{

    private $container; // container de dépendances de l'app

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function sayHello(Request $rq, Response $rs, array $args): Response
    {

        $p = $rq->getQueryParam('p', 0);
        $name = $args['name'];

        // $dbfile = $this->settings['dbfile']; s'il était dans index.php.
        // comme on a injecté le conteneur, c'est $this->container

        $dbfile = $this->container->settings['dbfile'];

        $rs->getBody()->write("<h1>Hello, $name</h1><h2>$dbfile : $dbfile</h2><h2>p = $p</h2>");
        return $rs;
    }

    public function welcome(Request $rq, Response $rs, array $args): Response
    {

        // Le routeur est enregistré dans le contenur de dépendance slim
        $urld = $this->container->router->pathFor('hello', ['name' => 'Marcello']);
        $urlc = $this->container->router->pathFor('hello', ['name' => 'Giuseppe']);

        // $html = $this->container->formatter("Hello dude");
        // $html = $this->container->test("Hello Dude");
        $html = "<h1>Hello dude</h1>";
        $html .= "<p><a href='$urld'>Ciao Marcello</a></p>";
        $html .= "<p><a href='$urlc'>Ciao Giuseppe</a></p>";

        $rs->getBody()->write($html);

        return $rs;
    }

    public function test_error(Request $req, Request $resp): Response
    {
        $reqUri = $req->getUri(); // ? Vide ?
        $pathFor = $this->container->router->pathFor('video7_1');
        $method_received = $req->getMethod();;

        $body_msg = [
            'req uri' => $reqUri,
            'path for' => $pathFor,
            'methode' => $method_received
        ];

        $resp = $resp->withStatus(202)
            ->withHeader('application-header', 'some value')
            ->withHeader("Content-Type", "application/json");
        //  ->withHeader('Allow', implode(', ', $methods));

        $resp->getBody()->write(json_encode($body_msg));
        return $resp;
    }

    public function uuid_test(Request $req, Response $resp): Response
    {
        $uuid = $this->container->uuid;

        $uuid1 = $uuid(1);
        $uuid4 = $uuid(4);

        $uuid3 = $uuid(3, Uuid::NAMESPACE_DNS, 'lbs.local');
        $uuid5 = $uuid(5, Uuid::NAMESPACE_DNS, 'lbs.local');

        //* tous les ids basés sur une séquence sont prévisibles

        $msg = [
            "Uuid v1 : basée sur le temps" => $uuid1,
            "Uuid v4 : basée sur un random" => $uuid4,
            "Uuid v3 : basée sur un nom ('lbs.local')" => $uuid3,
            "Uuid v5 : basée sur un nom, pour identifier 1 noeaud indépendament du hostname" => $uuid5,
        ];

        $resp = $resp->withStatus(200)
            ->withHeader("Content-Type", "application/json");

        $resp->getBody()->write(json_encode($msg));
        return $resp;
    }

    //* les fonctions rand(), mt_rand(), uniqid() ne
    //* sont pas de qualité cryptographique, même combinée avec un hash
    //* utiliser openssl_random_pseudo_bytes(),
    //* random_int(), random_bytes() :
    public function token_test(Request $req, Response $resp): Response
    {
        $token1 = random_bytes(32);
        $token1 = bin2hex($token1);
        
        $token2 = openssl_random_pseudo_bytes(32);
        $token2 = bin2hex($token2);

        $token3 = random_int(1, 32);

        $msg = [
            "token random byte + bin2hex" => $token1,
            "token openss random pseudo bytes + bin2hex" => $token2,
            "token not finihsed + random int" => $token3,
        ];

        $resp = $resp->withStatus(200)
            ->withHeader("Content-Type", "application/json");

        $resp->getBody()->write(json_encode($msg));
        return $resp;
    }


}
