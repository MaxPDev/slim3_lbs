<?php

use Slim\Container;

return [

    'settings' => [
        'displayErrorDetails' => true,
        'dbfile' => __DIR__ . '/commande.db.conf.ini.dist',
        // 'debug.log' => __DIR__ . '/../log/debug.log',
        // 'log.lovel' => \Monolog\Logger::DEBUG,
        // 'log.name' => 'slim.log'
    ],

        // On injecte le contenur pour récupérer la value de
    'dbhost' => function(Container $container) {
        $config = parse_ini_file($container->settings['dbfile']) ;
        return $config['host'];
    },

    // ??
    'formatter' => function () {
        return function (string $text) {
            return "<h1>$text</h1>";
        };
    },

    // ??
    'test' => function($x) {
        return "<h1>$x</h1>";
    }


];
