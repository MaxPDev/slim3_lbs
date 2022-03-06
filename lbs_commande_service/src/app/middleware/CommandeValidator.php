<?php
namespace lbs\command\app\middleware;
use  \Respect\Validation\Validator as V;

class CommandeValidator{

    public static function create_validators(){
        // Règles de validations :
        return [
            'nom' => V::StringType()->alpha(),
            'mail' => V::email(),
            'livraison' => [
                'date'=> V::date('d-m-Y')->min('now'),
                'heure' => V::date('H:i')
            ],
            'items' => V::arrayVal()->each(V::arrayVal()
            ->key('uri', V::StringType())
            ->key('q', V::intVal())
            ->key('libelle', V::StringType())
            ->key('tarif',V::floatVal()),
            )
        ];

    }
}
