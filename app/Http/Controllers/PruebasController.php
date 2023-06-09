<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Publicar;
use App\User;

class PruebasController extends Controller
{
    public function index () {
        $lenguajes = ["php","java"];

        return view("pruebas.index", array(
            'lenguajes' => $lenguajes
        ));

    }

    public function testOrm(){

        //usuario
       /* $usuarios = User::all();
        foreach($usuarios as $usuario){
            echo "<h1>". $usuario->name."</h1>";
            for($x=0;$x<$usuario->publicar->count() ;$x++){
                echo "<h3> el usuario ".$usuario->name." tiene ".$usuario->publicar->count()." publicaciones</h3>";
            }              
        }*/
        echo "<h1>usuarios y publicaciones</h1>";
        $usuarios = User::with('publicar')->get()->groupBy('name');
foreach($usuarios as $nombre => $usuarios_con_mismo_nombre){
    echo "<h1>". $nombre."</h1>";
    $num_publicaciones = 0;
    foreach($usuarios_con_mismo_nombre as $usuario){
        $num_publicaciones += $usuario->publicar->count();
    }
    echo "<h3> el usuario ".$nombre." tiene ".$num_publicaciones." publicaciones</h3>";
}
        /*foreach($usuarios as $usuario){
            echo "<h1>". $usuario->email."</h1>";
            $num_publicaciones = $usuario->publicar->count();
            echo "<h3> el usuario ".$usuario->email." tiene ".$num_publicaciones." publicaciones</h3>";
        }*/

        /*$usuarios = User::with('publicar')->get();
        foreach($usuarios as $usuario){
            echo "<h1>". $usuario->email."</h1>";
            echo "<h3> el usuario ".$usuario->email." tiene ".$usuario->publicar->count()." publicaciones</h3>";
        }*/

        $publicars = Publicar::all();
        foreach($publicars as $public){
            echo "<h1> Titulo del post:". $public->name . " y su creador es : ".$public->user->name."</h1>";
        }
        die();

    }
}
