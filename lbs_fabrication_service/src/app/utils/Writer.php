<?php

namespace lbs\fab\app\utils;

use \Psr\Http\Message\ResponseInterface as Response;
// use Psr\Container\ContainerInterface;


class Writer extends \Exception
{

    public static function json_error(Response $resp, int $code_error, string $msg): Response
    {

        // message d'erreur
        $data = [
            'type' => 'error',
            'error' => $code_error,
            'message' => $msg
        ];

        // header response
        $resp = $resp->withStatus($code_error)
            ->withHeader('Content-Type', 'application/json; charset=utf-8');

        $resp->getBody()->write(json_encode($data));

        return $resp;
    }

    // Code réponse HTTP + Header JSON (//? Rajouter data ?)
    public static function json_output(Response $resp, int $code_resp): Response
    {

        $resp = $resp->withStatus($code_resp)
                     ->withHeader('Content-Type', 'application/json; charset=utf-8');

        return $resp;
    }
}
