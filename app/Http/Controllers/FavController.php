<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Fav;
use App\User;

class FavController extends Controller
{
    public function __construct(){
        $this->middleware('api.auth', ['except' => ['index','show']]);
    }

    //url para saber las inscripcines que postula el usuario la usare en el proximo get
    //$ofertas = Inscripcion::with(['oferta', 'user'])->where('idusuario', 31)->get();
    public function index(){
        //$ofertas = Inscripcion::all()->load('oferta');
        /** si este da muchos problemas
         *  ponerlo mas simple
        */
        $ofertas = Inscripcion::with(['oferta', 'user'])->get();
        

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'ofertas' => $ofertas
        ]);
    }


    //mis candidaturas postuladas desde el usuario normal
    public function misFav(Request $request){
        $token = $request->header("Authorization");
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
        $user = $jwtAuth->checkToken($token,true);
        $fav = Fav::where('idusuario', $user->sub)->select('idusuario','idoferta')->distinct()->get()->load('oferta');
            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'ofertas.',
                'total' => $fav->count(),
                'publicaciones' => $fav
            ];
        return response()->json($data,$data['code']);

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
            'idoferta' => 'required'
        ]);

        if($validate->fails()){
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'no se ha guardado la fav.'
            ];
        }else{
            $user = $jwtAuth->checkToken($token,true);
            //$fecha_actual = date("Y-m-d");
            $fav = new Fav();
            $fav->idoferta = $params_array['idoferta'];
            $fav->idusuario = $user->sub;
            $fav->save();
            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'Nueva publicación.',
                'oferta' => $fav
            ];
        }
        return response()->json($data,$data['code']);
    }

    public function destroy($id){
        $fav = Fav::where('id', $id)->delete();
        $data = [
            'code' => 200,
            'status' => 'success',
            'message' => 'fav borrada.'
        ];
        return response()->json($data,$data['code']);
    }

    public function destroyFav($idusuario,$idoferta){
        $fav = Fav::where([['idusuario', $idusuario],['idoferta', $idoferta]])->delete();
        $data = [
            'code' => 200,
            'status' => 'success',
            'message' => 'fav borrada.'
        ];
        return response()->json($data,$data['code']);
    }

    public function verificarFav($idusuario,$idoferta){
        $favCount = Fav::where([['idusuario', $idusuario],['idoferta', $idoferta]])->count();
        $data = [
            'code' => 200,
            'status' => 'success',
            'message' => $favCount
        ];
        return response()->json($data,$data['code']);
    }
}
