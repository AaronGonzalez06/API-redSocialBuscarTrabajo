<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    protected $table = 'inscripcion';

    protected $fillable = [
        'id','estado','idusuario','idoferta','cv',
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
