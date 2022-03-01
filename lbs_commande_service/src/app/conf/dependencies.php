<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Ramsey\Uuid\Uuid;
use Slim\Container;

// Services

return [

    // On injecte le contenur pour récupérer la value de (automatiquement injecté selon la syntaxe)
    'dbhost' => function (Container $container) {
        $config = parse_ini_file($container->settings['dbconf']);
        return $config['host'];
    },

    // enregistrer msg quand besoind dans l'app
    // 'logger' => function (Container $container) {
    //     // évidemment ne pas enregistrer en dur. Configurer les var dans settings
    //     // On peut créer plusieurs fichier de log
    //     $log = new Logger($container->settings['log.name']);
    //     // level :  debug3, info, notice, warning, error, critical_error. Indiquer nv minimum
    //     // à partir duquel enregistrer          (fichier, niveau)
    //     $log->pushHandler(new StreamHandler($container->settings['debug.log'],
    //                                     $container->settings['log.level']));
    //     return $log;
    // },

    // * au lieur créer un seul 'logger', on en créer un par type : debug, error etc... :

    // logger debug
    'logger.debug' => function (Container $container) {
        $log = new Logger($container->settings['debug.name']);                  //* Nom du log
        $log->pushHandler(new StreamHandler(
            $container->settings['debug.log'],     //* Nom du fichier du log
            $container->settings['debug.level']
        )); //* Niveau de base du log
        return $log;
    },

    // logger warn
    'logger.warn' => function (Container $container) {
        $log = new Logger($container->settings['warn.name']);
        $log->pushHandler(new StreamHandler(
            $container->settings['warn.log'],
            $container->settings['warn.level']
        ));
        return $log;
    },

    // logger error
    'logger.error' => function (Container $container) {
        $log = new Logger($container->settings['error.name']);
        $log->pushHandler(new StreamHandler(
            $container->settings['error.log'],
            $container->settings['error.level']
        ));
        return $log;
    },

    //markdown 2 html
    'md2html' => function (Container $container) {
        return function (string $md) {
            //     $parser = new Parsedown();
            //     return $parser->text($md); 

            //* changement de service md2html facile:
            //* on change les services ici, principe de contenur d'injecteur de dépendance

            return \Michelf\Markdown::defaultTransform($md);
        };
    },

    // logger créable autrement
    // mais placer dans un conteneur, plus flexible
    // adaptable à la condition dans laquelle on intalle l'app

    // Uuid generator
    'uuid' => function (Container $container) {
        return function ($v, $param1 = null, $param2 = null) {
            $uuidx = 'uuid' . $v;
            return Uuid::$uuidx($param1, $param2)->toString();
        };
    },

    // Token generator : random_int :
    'token' => function (Container $container) {
        return function ($nb_int) {
            $token = random_bytes($nb_int);
            $token = bin2hex($token);
            return $token;
        };
    },

];
