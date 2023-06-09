<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Oferta;
use App\User;
use App\Http\Controllers\DB;


class OfertaController extends Controller
{
    public function __construct(){
        $this->middleware('api.auth', ['except' => ['index','show','ofertaBusqueda','cantidadEmpleo','ofertaBusquedaAvanzada','ofertaId','totalOfertaEmpresa']]);
    }

    /**
     * con este metodo hacemos que se muestren todas las ofertas que hay
     * donde ira para la pestalla de ofertas.
     */
    public function index(){
        //$ofertas = Publicar::all()->load('user');
        //$ofertas = Oferta::all()->load('user');
        $ofertas = Oferta::where('estado', 'activa')->orderBy('fecha', 'desc')->orderBy('id', 'desc')->get()->load('user');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'ofertas' => $ofertas
        ]);
    }

    /**
     * este metodo ira para la la ventana de crear oferta de empleo
     */

    public function store(Request $request){

        //comprobar que el usuario esta identificado
        $token = $request->header("Authorization");
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

        $json = $request->input('json',null);

        $params_array = json_decode($json,true);            

        $validate = \Validator::make($params_array,[
            'titulo' => 'required',
            'subtitulo' => 'required',
            'cuerpo' => 'required',
            'ubucacion' => 'required',
            'localidad' => 'required',
            'sector' => 'required',
            'tipoContrato' => 'required',
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
            $oferta = new Oferta();
            $oferta->titulo = $params_array['titulo'];
            $oferta->subtitulo = $params_array['subtitulo'];
            $oferta->cuerpo = $params_array['cuerpo'];
            $oferta->ubucacion = $params_array['ubucacion'];
            $oferta->localidad = $params_array['localidad'];
            $oferta->sector = $params_array['sector'];
            $oferta->tipoContrato = $params_array['tipoContrato'];
            $oferta->estado = "activa";
            $oferta->user_id = $user->sub;
            $oferta->fecha = $fecha_actual;
            $oferta->save();
            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'Nueva publicación.',
                'oferta' => $oferta
            ];
        }
        return response()->json($data,$data['code']);
    }

    public function misOfertas(Request $request){
        $token = $request->header("Authorization");
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
        $user = $jwtAuth->checkToken($token,true);
        //$ofertas = Oferta::where([['user_id', $user->sub],['estado','=','activa']])->get();
        //$ofertas = Oferta::where([['user_id', $user->sub]])->get();
        $ofertas = Oferta::where('user_id', $user->sub)->orderBy('fecha', 'desc')->orderBy('id', 'desc')->get();
            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'ofertas.',
                'total' => $ofertas->count(),
                'publicaciones' => $ofertas
            ];
        return response()->json($data,$data['code']);

    }

    public function destroy($id){
        $oferta = Oferta::where('id', $id)->delete();
        $data = [
            'code' => 200,
            'status' => 'success',
            'message' => 'oferta borrada.'
        ];
        return response()->json($data,$data['code']);
    }

    public function show($id){
        $oferta = Oferta::find($id)->load('user');

        if(is_object($oferta)){
            $data = [
                'code' => 200,
                'status' => 'success',
                'publicacion' => $oferta
            ];
        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'oferta' => 'La oferta no existe'
            ];
        }
        return response()->json($data, $data['code']);
    }

    //para hacer las busquedas de empresas: 
    public function ofertaBusqueda(Request $request){
        $json = $request->input('json',null);

        $params_array = json_decode($json,true);            

        $validate = \Validator::make($params_array,[
            'localidad' => 'required'
        ]);

        if($validate->fails()){
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error.'
            ];
        }else{
            //$Ofertas = Oferta::where('localidad', $params_array['localidad'])->get();
            $Ofertas = Oferta::where('localidad', $params_array['localidad'])->where('estado', 'activa')->orderBy('fecha', 'desc')->orderBy('id', 'desc')->with('user')->get();
            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'Datos.',
                'total' => $Ofertas->count(),
                'result' => $Ofertas
            ];
        }
        return response()->json($data,$data['code']);

    }

    public function cantidadEmpleo(){
        //$ofertas = Publicar::all()->load('user');
        $ofertas = \DB::table('oferta')->where('estado', 'activa')->selectRaw('localidad, COUNT(*) as total')->groupBy('localidad')->get();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'cantidad' => $ofertas
        ]);
    }

    //para hacer las busquedas de empresas: 
    public function ofertaBusquedaAvanzada($campo){
        $Ofertas = Oferta::where('localidad', 'like', '%'.$campo.'%')
                            ->orWhere('titulo', 'like', '%'.$campo.'%')
                            ->orWhere('cuerpo', 'like', '%'.$campo.'%')
                            ->with('user')
                            ->orderBy('fecha', 'desc')
                            ->get();
        $data = [
            'code' => 200,
            'status' => 'success',
            'message' => 'Datos.',
            'total' => $Ofertas->count(),
            'result' => $Ofertas
        ];
        
        return response()->json($data,$data['code']);

    }

    // oferta de un perfil
    public function ofertaId($id){
        $ofertas = Oferta::where('user_id', $id)->where('estado', 'activa')->orderBy('fecha', 'desc')->orderBy('id', 'desc')->with('user')->get();
        $data = [
            'code' => 200,
            'status' => 'success',
            'message' => 'tus publicaciones.',
            'total' => $ofertas->count(),
            'publicaciones' => $ofertas
        ];
    return response()->json($data,$data['code']);
}

public function finalizarOferta($idusuario,$idoferta){
    $oferta = Oferta::where([
                            ['id', '=', $idoferta]
                    ])->update(['estado' => 'Finalizada','candidatoSeleccionado' =>$idusuario]);
    $data = [
        'code' => 200,
        'status' => 'success',
        'message' => 'Oferta finalizada.'
    ];
    return response()->json($data,$data['code']);
}

public function totalOfertaEmpresa($idusuario){
    $ofertas = Oferta::where('user_id', $idusuario)->count();
    $data = [
        'code' => 200,
        'status' => 'success',
        'totalOfertas' => $ofertas
    ];
    return response()->json($data,$data['code']);
}

public function updateOferta(Request $request){

    //comprobar que el usuario esta identificado
    $token = $request->header("Authorization");
    $jwtAuth = new \JwtAuth();
    $checkToken = $jwtAuth->checkToken($token);

    //obtener datos por post
    $json = $request->input('json',null);
    $params_array = json_decode($json,true);

    if($checkToken && !empty($params_array)){
        //validar datos
        $validate = \Validator::make($params_array, [
            'titulo'   => 'required',
            'subtitulo' => 'required',
            'cuerpo' => 'required',
            'ububacion' => 'required',
            'localidad'   => 'required',
            'sector' => 'required',
            'tipoContrato' => 'required',
            'id' => 'required'
        ]);

        $oferta_update = Oferta::where('id', $params_array['id'])->update($params_array);

        //devolver resultado
        $data = array(
            'code' => 200,
            'status' => 'success',
            'changes' => $params_array
        );


    }else{
        $data = array(
            'code' => 400,
            'status' => 'error',
            'message' => 'La empresa no está identificado.',
        );
    }
    return response()->json($data,$data['code']);
}

}
