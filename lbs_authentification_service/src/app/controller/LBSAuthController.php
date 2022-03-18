<?php

/**
 * Created by PhpStorm.
 * User: canals5
 * Date: 18/11/2019
 * Time: 15:27
 */

namespace lbs\auth\app\controller;


use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\auth\app\models\User;
use lbs\auth\app\utils\Writer;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


/**
 * Class LBSAuthController
 * @package lbs\auth\app\controller
 */
class LBSAuthController
{
    private $container; // le conteneur de dépendences de l'application


    public function __construct(\Slim\Container $container)
    {
        $this->container = $container;
    }


    // public function test(Request $req, Response $resp): Response
    // {


    //     // Construction des donnés à retourner dans le body
    //     $datas_resp = [
    //         "type" => "collection",

    //     ];

    //     $resp = $resp->withStatus(200);
    //     $resp = $resp->withHeader('application-header', 'TD 1');
    //     $resp = $resp->withHeader("Content-Type", "application/json;charset=utf-8");

    //     $resp->getBody()->write(json_encode($datas_resp));

    //     return $resp;
    // }


    public function authenticate(Request $rq, Response $rs, $args): Response
    {

        if (!$rq->hasHeader('Authorization')) {

            $rs = $rs->withHeader('WWW-authenticate', 'Basic realm="commande_api api" ');
            return Writer::json_error($rs, 401, 'No Authorization header present');
        };

        $authstring = base64_decode(explode(" ", $rq->getHeader('Authorization')[0])[1]);
        list($email, $pass) = explode(':', $authstring);

        try {
            $user = User::select('id', 'email', 'username', 'passwd', 'refresh_token', 'level')
                ->where('email', '=', $email)
                ->firstOrFail();

            if (!password_verify($pass, $user->passwd))
                throw new \Exception("password check failed");
        } catch (ModelNotFoundException $e) {
            $rs = $rs->withHeader('WWW-authenticate', 'Basic realm="lbs auth" ');
            return Writer::json_error($rs, 401, 'Erreur authentification1');
        } catch (\Exception $e) {
            $rs = $rs->withHeader('WWW-authenticate', 'Basic realm="lbs auth" ');
            return Writer::json_error($rs, 401, 'Erreur authentification2 ' . $e->getMessage());
        }

        //*  "iss" : "issuer", identifie l'émetteur du token
        //*  "sub" : "subject", le sujet du token
        //*  "aud" : "audience", destinataires du token – le serveur
        //* r       recevant un token doit vérifier qu'il lui est bien destiné
        //*  "iat" : "issued at", date d'émission du token
        //*  "exp" : "expires", date d'expiration du token
        //*  "nbf" : "not before", date de validité du token
        //*  "jti" : "jwt id", identificateur unique du token

        $secret = $this->container->settings['secret'];
        $token = JWT::encode(
            [
                'iss' => 'http://api.auth.local/auth',
                'aud' => 'http://api.backoffice.local',
                'iat' => time(),
                'exp' => time() + (12 * 30 * 24 * 3600),
                'upr' => [
                    'email' => $user->email,
                    'username' => $user->username,
                    'level' => $user->level
                ]
            ],
            $secret,
            'HS512'
        );

        $user->refresh_token = bin2hex(random_bytes(32));
        $user->save();
        $data = [
            'access-token' => $token,
            'refresh-token' => $user->refresh_token
        ];

        $rs->getBody()->write(json_encode($data));
        return Writer::json_output($rs, 200);
    }

    public function check(Request $req, Response $resp, $args): Response
    {

        try {

            $secret = $this->container->settings['secret'];

            $h = $req->getHeader('Authorization')[0];
            $tokenstring = sscanf($h, "Bearer %s")[0];
            $token = JWT::decode($tokenstring, new Key($secret, 'HS512'));

            $data = [
                'user_mail' => $token->upr->email,
                'user_username' => $token->upr->username,
                'user_level' => $token->upr->level,
            ];

            $resp->getBody()->write(json_encode($data));

            return Writer::json_output($resp, 200); //? Mettre data dans output ?
        } catch (ExpiredException $e) {
            return Writer::json_error($resp, 401, 'The token is expired');
        } catch (SignatureInvalidException $e) {
            return Writer::json_error($resp, 401, 'The signature is not valid');
        } catch (BeforeValidException $e) {
            return Writer::json_error($resp, 401, 'BeforeValidException');
        } catch (\UnexpectedValueException $e) {
            return Writer::json_error($resp, 401, 'The value of token is not the right one');
        }

        return $resp;
    }
}
