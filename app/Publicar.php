<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Publicar extends Model
{
    protected $table = 'publicar';


    protected $fillable = [
        'name','user_id','subtitulo','cuerpo','foto','id','fecha'
    ];

    /*public function posts(){
        return $this->hasMany('App\User');
    }*/


    //relacion uno a muchos
    public function comentar(){
        return $this->hasMany('App\Comentario');
    }

    //relacion inversa
    public function user(){
        return $this->belongsTo('App\User','user_id');
    }
}
