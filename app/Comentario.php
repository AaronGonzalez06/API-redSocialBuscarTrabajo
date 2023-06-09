<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    protected $table = 'comentario';

    protected $fillable = [
        'id','cuerpo','fecha','idusuario','idpublicacion',
    ];

    /*public function posts(){
        return $this->hasMany('App\User');
    }*/

    //relacion inversa
    //por ahora no la vamos a hacer
    public function publicar(){
        return $this->belongsTo('App\Publicar','idpublicacion');
    }
    
    //prueba. borrar si no funciona
    public function user(){
        return $this->belongsTo('App\User','idusuario');
    }
}
