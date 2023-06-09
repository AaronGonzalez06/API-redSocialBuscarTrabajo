<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Comentario;
use App\User;

class ComentarioController extends Controller
{
    public function __construct(){
        $this->middleware('api.auth', ['except' => ['index','show','comentariosDePublicaciones',]]);
    }

    //probar si funciona no esta hecha nada
    public function store(Request $request){

        //comprobar que el usuario esta identificado
        $token = $request->header("Authorization");
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

        $json = $request->input('json',null);

        $params_array = json_decode($json,true);            

        $validate = \Validator::make($params_array,[
            'cuerpo' => 'required',
            'idpublicacion' => 'required'
        ]);

        if($validate->fails()){
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'no se ha guardado el comentario.'
            ];
        }else{
            $user = $jwtAuth->checkToken($token,true);
            $fecha_actual = date("Y-m-d");
            $comentario = new Comentario();
            $comentario->cuerpo = $params_array['cuerpo'];
            $comentario->idusuario = $user->sub;
            $comentario->fecha = $fecha_actual;
            $comentario->idpublicacion = $params_array['idpublicacion'];
            $comentario->save();
            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'Nueva comentario.',
                'oferta' => $comentario
            ];
        }
        return response()->json($data,$data['code']);
    }

    public function comentariosDePublicaciones(Request $request){
        
        $json = $request->input('json',null);

        $params_array = json_decode($json,true);            

        $validate = \Validator::make($params_array,[
            'idpublicacion' => 'required'
        ]);
        //->load('user')
        $comentarios = Comentario::where('idpublicacion', $params_array['idpublicacion'] )->orderBy('fecha', 'desc')->orderBy('id', 'desc')->get()->load('user');
            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'ofertas.',
                'total' => $comentarios->count(),
                'comentarios' => $comentarios
            ];
        return response()->json($data,$data['code']);

    }

}
