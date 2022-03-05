<?php

// Fichier d'essais

return [
    'test' => function($container) {
        return function($text) {
            return "<h1>$text</h1>";   
        };
    }

];
