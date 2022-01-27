<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Slim\Container;

// Services

return [

    // On injecte le contenur pour récupérer la value de
    'dbhost' => function (Container $container) {
        $config = parse_ini_file($container->settings['dbfile']);
        return $config['host'];
    },

    // enregistrer msg quand besoind dans l'app
    'logger' => function (Container $container) {
        // évidemment ne pas enregistrer en dur. Configurer les var dans settings
        // On peut créer plusieurs fichier de log
        $log = new Logger($container->settings['log.name']);
        // level :  debug3, info, notice, warning, error, critical_error. Indiquer nv minimum
        // à partir duquel enregistrer          (fichier, niveau)
        $log->pushHandler(new StreamHandler($container->settings['debug.log'],
                                        $container->settings['log.level']));
        return $log;
    },

    //markdown 2 html
    'md2html' => function(Container $container) {
        return function(string $md) {
            $parser = new Parsedown();
            return $parser->text($md); //video6 25;00
        };
    }

    // logger créable autrement
    // mais placer dans un conteneur, plus flexible
    // adaptable à la condition dans laquelle on intalle l'app

];
