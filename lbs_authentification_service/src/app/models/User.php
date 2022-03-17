<?php

namespace lbs\auth\app\models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    // protected $fillable = array(
    //     'id', 'email', 'username', 'passwd', 'refresh_token', 'level', 'created_at', 'updated_at'
    // );
}
