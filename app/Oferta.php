<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Oferta extends Model
{
    protected $table = 'oferta';

    protected $fillable = [
        'id','titulo','subtitulo','cuerpo','ubucacion','localidad','sector','fecha','user_id','candidatoSeleccionado','tipoContrato'
    ];

    /*public function posts(){
        return $this->hasMany('App\User');
    }*/

    //relacion inversa
    public function user(){
        return $this->belongsTo('App\User','user_id');
    }

    //relacion uno a muchos prueba
    public function inscripcion(){
        return $this->hasMany('App\Inscripcion');
    }

    //relacion uno a muchos prueba
    public function fav(){
        return $this->hasMany('App\Fav');
    }
}
