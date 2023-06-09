<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Inscripcion;
use App\User;

class InscripcionController extends Controller
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
    public function misInscripciones(Request $request){
        $token = $request->header("Authorization");
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
        $user = $jwtAuth->checkToken($token,true);
        $inscripcion = Inscripcion::where('idusuario', $user->sub)->select('idusuario','idoferta','estado')->distinct()->get()->load('oferta');
            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'ofertas.',
                'total' => $inscripcion->count(),
                'publicaciones' => $inscripcion
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
            'cv' => 'required',
            'idoferta' => 'required'
        ]);

        if($validate->fails()){
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'no se ha guardado la publicación.'
            ];
        }else{
            $user = $jwtAuth->checkToken($token,true);
            //$fecha_actual = date("Y-m-d");
            $inscripcion = new Inscripcion();
            $inscripcion->cv = $params_array['cv'];
            $inscripcion->idusuario = $user->sub;
            $inscripcion->idoferta = $params_array['idoferta'];
            $inscripcion->estado = "En proceso";
            $inscripcion->save();
            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'Nueva publicación.',
                'oferta' => $inscripcion
            ];
        }
        return response()->json($data,$data['code']);
    }

    public function destroy($id){
        $inscripcion = Inscripcion::where('id', $id)->delete();
        $data = [
            'code' => 200,
            'status' => 'success',
            'message' => 'Inscripcion borrada.'
        ];
        return response()->json($data,$data['code']);
    }

    public function CandidatosParaOfertaEmpresa(Request $request){
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
                'message' => 'error.'
            ];
        }else{
            $user = $jwtAuth->checkToken($token,true);
            //$fecha_actual = date("Y-m-d");
            $datos = Inscripcion::where([['idoferta', $params_array['idoferta']], ['estado','!=','Descartado']])
            ->select('idusuario','idoferta')->distinct()
            ->get()->load('user');
            $data = [
                'code' => 200,
                'status' => 'success',
                'total' => '',
                'oferta' => $datos
            ];
        }
        return response()->json($data,$data['code']);

    }

    public function descartarCandidato($idusuario,$idoferta){
        $oferta = Inscripcion::where([
            ['idusuario', '=', $idusuario],
            ['idoferta', '=', $idoferta]
        ])->update(['estado' => 'Descartado']);
        $data = [
            'code' => 200,
            'status' => 'success',
            'message' => 'Candidato descartado.'
        ];
        return response()->json($data,$data['code']);
    }

    public function seleccionarCandidato($idusuario,$idoferta){
        $oferta = Inscripcion::where([
            ['idusuario', '=', $idusuario],
            ['idoferta', '=', $idoferta]
        ])->update(['estado' => 'Seleccionado']);
        $data = [
            'code' => 200,
            'status' => 'success',
            'message' => 'Candidato seleccionado.'
        ];
        return response()->json($data,$data['code']);
    }

    public function destroyInscripcion($idusuario,$idoferta){
        $fav = Inscripcion::where([['idusuario', $idusuario],['idoferta', $idoferta]])->delete();
        $data = [
            'code' => 200,
            'status' => 'success',
            'message' => 'Inscripcion borrada.'
        ];
        return response()->json($data,$data['code']);
    }

    public function verificarInscripcion($idusuario,$idoferta){
        $InscripcionCount = Inscripcion::where([['idusuario', $idusuario],['idoferta', $idoferta]])->count();
        $data = [
            'code' => 200,
            'status' => 'success',
            'message' => $InscripcionCount
        ];
        return response()->json($data,$data['code']);
    }
}
