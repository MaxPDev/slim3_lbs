<?php

use Slim\Container;


// En général, dans le settings.php on laisse exclusivement les settings
// Ranger autres dépendances aillleurs.

return [

    'settings' => [
        'displayErrorDetails' => true,
        'dbfile' => __DIR__ . '/commande.db.conf.ini.dist',
        'debug.log' => __DIR__ . '/../log/warn.log',
        'log.level' => \Monolog\Logger::DEBUG, // tt les msg à partir du nv debug seront rec
        'log.name' => 'slim.log'
    ],

    // Problème de permission :
    // chown -R www-data:www-data debug.log
    // Si le dev fait chmod 777, passoire ?

    // // ??
    // 'formatter' => function () {
    //     return function (string $text) {
    //         return "<h1>$text</h1>";
    //     };
    // },

    // // ??
    // 'test' => function($x) {
    //     return "<h1>$x</h1>";
    // }


];
