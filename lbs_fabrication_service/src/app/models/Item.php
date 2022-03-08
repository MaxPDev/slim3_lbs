<?php

namespace lbs\fab\app\models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends \Illuminate\Database\Eloquent\Model {

    protected $table      = 'item';  /* le nom de la table */
    protected $primaryKey = 'id';

    public  $incrementing = true;      //pour primarykey, on annule l'auto_increment
    public $timestamps = false;

    // protected $fillable = array(
    //     'id', 'uri', 'libelle', 'tarif', 'quantite', 'command_id'
    // );



    public function commande() {
        return $this->belongsTo('\lbs\fab\app\models\Commande', 'command_id');
    }



}
