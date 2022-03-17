<?php

use Slim\Container;


// En général, dans le settings.php on laisse exclusivement les settings
// Ranger autres dépendances aillleurs.

return [

    'settings' => [
        'displayErrorDetails' => true,

        'dbconf' => __DIR__ . '/user.db.conf.ini.dist',

        'debug.name' => 'lbs.log',
        'debug.log' => __DIR__ . '/../log/debug.log',
        'debug.level' => \Monolog\Logger::DEBUG, // tt les msg à partir du nv debug seront rec

        'warn.name' => 'lbs.log',
        'warn.log' => __DIR__ . '/../log/warn.log',
        'warn.level' => \Monolog\Logger::WARNING, // or WARN ??

        'error.name' => 'lbs.log',               //* Nom du log     
        'error.log' => __DIR__ . '/../log/error.log',  //* Nom du fichier du log    
        'error.level' => \Monolog\Logger::ERROR,       //* Niveau de base du log

        'secret' => '1er7cfajoms82egsxqswexdcftvgb482515s',
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

    // ??


];


// \Slim\Log::EMERGENCY
//     Level 1
// \Slim\Log::ALERT
//     Level 2
// \Slim\Log::CRITICAL
//     Level 3
// \Slim\Log::ERROR
//     Level 4
// \Slim\Log::WARN
//     Level 5
// \Slim\Log::NOTICE
//     Level 6
// \Slim\Log::INFO
//     Level 7
// \Slim\Log::DEBUG
//     Level 8 