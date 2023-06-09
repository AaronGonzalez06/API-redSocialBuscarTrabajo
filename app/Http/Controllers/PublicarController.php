<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Publicar;
use App\User;

class PublicarController extends Controller
{
    public function __construct(){
        $this->middleware('api.auth', ['except' => ['index','show','indexPersonal','indexSearch','PublicacionesId']]);
    }

    //quitando el load, dejamos de mostrar las información asociada a la publicación
    public function index(){
        //$publicaciones = Publicar::all()->orderBy('fecha', 'desc')->load('user');
        $publicaciones = Publicar::orderBy('fecha', 'desc')->orderBy('id', 'desc')
                    ->with('user')
                    ->get();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'publicaciones' => $publicaciones
        ]);
    }

    public function indexPersonal(){
        //$publicaciones = Publicar::where('user_id', 7)->get()->load('user');
        $publicaciones = Publicar::where('user_id', 7)->orderBy('fecha', 'desc')->orderBy('id', 'desc')->get();
        $usuario = User::where('id',7)->get();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'usuario' => $usuario,
            'publicaciones' => $publicaciones
        ]);
    }

    public function show($id){
        $publicacion = Publicar::find($id);

        if(is_object($publicacion)){
            $data = [
                'code' => 200,
                'status' => 'success',
                'publicacion' => $publicacion
            ];
        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'publicacion' => 'La publicacion no existe'
            ];
        }
        return response()->json($data, $data['code']);
    }

    //guardar publicacion
    //a priori no se va a poder modificar las publicaciones
    public function store(Request $request){

        //comprobar que el usuario esta identificado
        $token = $request->header("Authorization");
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

        $json = $request->input('json',null);

        $params_array = json_decode($json,true);            

        $validate = \Validator::make($params_array,[
            'name' => 'required',
            'cuerpo' => 'required',
            'subtitulo' => 'required',
        ]);

        if($validate->fails()){
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'no se ha guardado la publicación.'
            ];
        }else{
            $user = $jwtAuth->checkToken($token,true);
            $fecha_actual = date("Y-m-d");
            $publicar = new Publicar();
            $publicar->name = $params_array['name'];
            $publicar->cuerpo = $params_array['cuerpo'];
            $publicar->subtitulo = $params_array['subtitulo'];
            $publicar->user_id = $user->sub;
            $publicar->fecha = $fecha_actual;
            $publicar->save();
            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'Nueva publicación.'
            ];
        }
        return response()->json($data,$data['code']);
    }

    //mis publicaciones
    public function misPublicaciones(Request $request){

        //comprobar que el usuario esta identificado
        $token = $request->header("Authorization");
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

        /*$json = $request->input('json',null);

        $params_array = json_decode($json,true);            

        $validate = \Validator::make($params_array,[
            'id' => 'required',
        ]);*/

        /*if($validate->fails()){
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'no se ha encontrado tus publicaciones.'
            ];
        }else{*/
            $user = $jwtAuth->checkToken($token,true);
            $publicaciones = Publicar::where('user_id', $user->sub)->orderBy('fecha', 'desc')->orderBy('id', 'desc')->get()->load('user');
            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'tus publicaciones.',
                'total' => $publicaciones->count(),
                'publicaciones' => $publicaciones
            ];
        //}
        return response()->json($data,$data['code']);
    }

    //borrar esta incompleto
    public function destroy($id){
        $publicaciones = Publicar::where('id', $id)->delete();
        $data = [
            'code' => 200,
            'status' => 'success',
            'message' => 'Publicación borrada.'
        ];
        return response()->json($data,$data['code']);
    }

    //buscar por email
    public function indexSearch($email){

        $user = User::where('email', $email)->first(); //Recuperamos el objeto completo del usuario
        $id = $user->id; //Obtenemos el ID del usuario y lo almacenamos en una variable independiente
        $publicaciones = Publicar::where('user_id', $id)->orderBy('fecha', 'desc')->orderBy('id', 'desc')->get()->load('user'); // Utilizamos la variable $id en la siguiente consulta de Eloquent

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'total' => $publicaciones->count(),
            'publicaciones' => $publicaciones
        ]);
    }

    // publicaciones de un perfil
    public function PublicacionesId($id){
            $publicaciones = Publicar::where('user_id', $id)->get()->load('user');
            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'tus publicaciones.',
                'total' => $publicaciones->count(),
                'publicaciones' => $publicaciones
            ];
        return response()->json($data,$data['code']);
    }

}
