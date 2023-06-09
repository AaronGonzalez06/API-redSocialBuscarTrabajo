<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fav extends Model
{
    protected $table = 'fav';

    protected $fillable = [
        'id','idusuario','idoferta',
    ];

    /*public function posts(){
        return $this->hasMany('App\User');
    }*/

    //relacion inversa
    //por ahora no la vamos a hacer
    public function oferta(){
        return $this->belongsTo('App\Oferta','idoferta');
    }
    
    //prueba. borrar si no funciona
    public function user(){
        return $this->belongsTo('App\User','idusuario');
    }
}
