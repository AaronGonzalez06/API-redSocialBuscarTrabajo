<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;
use App\Publicar;

class UserController extends Controller
{
    public function pruebas(Request $request){
        return "Acción de pruebas de USER-CONTROLLER";
    }

    public function registerUsuario(Request $request){
        
        // recoger los datos
        $json = $request->input('json',null);
        $param = json_decode($json); //devuelve un objeto
        $param_array = json_decode($json,true); //devuelve un array

        if(($param != null) &&  ($param_array !=null)){
            $param_array = array_map('trim', $param_array);
            // validar datos
        $validate = \Validator::make($param_array, [
            'DNI'   => 'required',
            'name'   => 'required|alpha',
            'surname'   => 'required|alpha',
            'password'   => 'required',
            'edad'   => 'required|integer',
            'telefono'   => 'required|integer',
            'email'   => 'required|email|unique:users',
            'provincia'   => 'required|alpha',
        ]);

        if($validate->fails()){
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'EL usuario no se ha creado',
                'errors' => $validate->errors()
            );

        }else{

            //cifrar
            //$pwd = password_hash($param->password, PASSWORD_BCRYPT, ['cost' => 4]);
            $pwd = hash('sha256',$param->password);

            //crear usuario
            $user = new User();
            $user->name = $param_array['name'];
            $user->surname = $param_array['surname'];
            $user->email = $param_array['email'];
            $user->password = $pwd;
            $user->telefono = $param_array['telefono'];
            $user->provincia = $param_array['provincia'];
            $user->edad = $param_array['edad'];
            $user->DNI = $param_array['DNI'];
            $user->idrol = 1;
            //guardar usuario
            $user->save();
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'usuario creado correctamente',
                'user' => $user
            );
        }

        }else{

            $data = array(
                'status' => 'success',
                'code' => 400,
                'message' => 'datos enviado de mala forma',
            );

        }
        return response()->json($data,$data['code']);
    }

    public function registerEmpresa(Request $request){
        
        // recoger los datos
        $json = $request->input('json',null);
        $param = json_decode($json); //devuelve un objeto
        $param_array = json_decode($json,true); //devuelve un array

        if(($param != null) &&  ($param_array !=null)){
            $param_array = array_map('trim', $param_array);
            // validar datos
        $validate = \Validator::make($param_array, [
            'CIF'   => 'required',
            'nombreEmpresa'   => 'required',
            'password'   => 'required',
            'email'   => 'required|email|unique:users',
            'provincia'   => 'required|alpha',
            'localidad' => 'required|alpha',
            'codigoPostal' => 'required|integer'
        ]);

        if($validate->fails()){
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'La empresa no se ha creado',
                'errors' => $validate->errors()
            );

        }else{

            //cifrar
            //$pwd = password_hash($param->password, PASSWORD_BCRYPT, ['cost' => 4]);
            $pwd = hash('sha256',$param->password);

            //crear usuario
            $user = new User();
            $user->email = $param_array['email'];
            $user->password = $pwd;
            $user->provincia = $param_array['provincia'];
            $user->CIF = $param_array['CIF'];
            $user->localidad = $param_array['localidad'];
            $user->codigoPostal = $param_array['codigoPostal'];
            $user->direccion = $param_array['direccion'];
            $user->nombreEmpresa = $param_array['nombreEmpresa'];
            $user->idrol = 2;
            //guardar usuario
            $user->save();
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'Empresa creada correctamente',
                'user' => $user
            );
        }

        }else{

            $data = array(
                'status' => 'success',
                'code' => 400,
                'message' => 'datos enviado de mala forma',
            );

        }
        return response()->json($data,$data['code']);
    }

    public function login(Request $request){
        $jwtAuth = new \JwtAuth();

        $json = $request->input('json',null);
        $params = json_decode($json);
        $params_array = json_decode($json,true);

        $validate = \Validator::make($params_array, [
            'email'   => 'required|email',
            'password'   => 'required',
        ]);

        if($validate->fails()){
            $singnup = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Error con los campos.',
                'errors' => $validate->errors(),
            );

        }else{
            $pwd = hash('sha256',$params->password);

            $singnup = $jwtAuth->signup($params->email, $pwd);

            if(!empty($params->gettoken)){
                $singnup = $jwtAuth->signup($params->email, $pwd,true);
            }

        }

        return response()->json($singnup,200);
    }

    public function update(Request $request){

        //comprobar que el usuario esta identificado
        $token = $request->header("Authorization");
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

        //obtener datos por post
        $json = $request->input('json',null);
        $params_array = json_decode($json,true);

        if($checkToken && !empty($params_array)){
            //actualizar el usuario
            //sacar usuario identificado
            $user = $jwtAuth->checkToken($token,true);
            //validar datos
            $validate = \Validator::make($params_array, [
                'name'   => 'required|alpha',
                'surname'   => 'required|alpha',
                'edad'   => 'required|integer',
                'telefono'   => 'required|integer',
                'provincia'   => 'required|alpha',
            ]);

            //actualizar usuario en la base de datos
            $user_update = User::where('id', $user->sub)->update($params_array);

            //devolver resultado
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $user,
                'changes' => $params_array,
            );


        }else{
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'El usuario no está identificado.',
            );
        }
        return response()->json($data,$data['code']);
    }

    public function updateEmpresa(Request $request){

        //comprobar que el usuario esta identificado
        $token = $request->header("Authorization");
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

        //obtener datos por post
        $json = $request->input('json',null);
        $params_array = json_decode($json,true);

        if($checkToken && !empty($params_array)){
            //actualizar el usuario
            //sacar usuario identificado
            $user = $jwtAuth->checkToken($token,true);
            //validar datos
            $validate = \Validator::make($params_array, [
                'provincia'   => 'required|alpha',
                'localidad' => 'required|alpha',
                'codigoPostal' => 'required|integer',
                'direccion' => 'required'
            ]);

            //actualizar usuario en la base de datos
            $user_update = User::where('id', $user->sub)->update($params_array);

            //devolver resultado
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $user,
                'changes' => $params_array,
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

    //sube la imagen de perfil del usuario
    public function upload(Request $request){

        //recoger datos 
        //la forma de llamarlo es asi porque ene front vamos
        //a usar una libreria que lo requiere
        $image = $request->file('file0');

        //validar imagen
        $validate = \Validator::make($request->all(), [
            //volver aqui para solucinar problema  validar imagen
            'file0'   => 'required'
        ]);

        //guardar imagen
        if(!$image || $validate->fails()){

            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'errores al subir la imagen',
            );

        } else{
            $image_name = time() . $image->getClientOriginalName();
            \Storage::disk('users')->put($image_name, \File::get($image));
            $data = array(
                'code' => 200,
                'status' => 'success',
                'message' => $image_name,
            );

            $token = $request->header("Authorization");
            $jwtAuth = new \JwtAuth();
            $user = $jwtAuth->checkToken($token,true);
            $user_update = User::where('id', $user->sub)->update(['image' => $image_name]);

            
        }
        return response()->json($data,$data['code']);
        //return response($data,$data['code'])->header('Content-Type','text/plain');

    }

    //saca la imagen de perfil
    public function getImage($filename){
        $isset = \Storage::disk('users')->exists($filename);
        if($isset){
            $file = \Storage::disk('users')->get($filename);
            return new Response($file,200);
        }else{
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'la imagen no existe',
            );
        }
        return response()->json($data,$data['code']);
    }

    public function getFile($filename){
        $isset = \Storage::disk('cv')->exists($filename);
        if($isset){
            $file = \Storage::disk('cv')->get($filename);
            return new Response($file,200);
        }else{
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'la imagen no existe',
            );
        }
        return response()->json($data,$data['code']);
    }

    //obtiene los datos del usuario
    public function detail($id){
        $user = User::find($id);

        if(is_object($user)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'message' => $user
            );
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'el usuario no existe',
            );
        }

        return response()->json($data,$data['code']);
    }

    //Mostrar todos los usuarios registrados
    public function all($idrol = null){
        if ($idrol) {
            $users = User::where('idrol', $idrol)->get();
        } else {
            $users = User::all();
        }
    
        if($users->count() > 0) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'total' => $users->count(),
                'message' => $users
            );
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'No se encontraron usuarios',
            );
        }
    
        return response()->json($data,$data['code']);
    }

    public function empresas(){
            $users = User::where('idrol', 2)->get();
        
    
        if($users->count() > 0) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'total' => $users->count(),
                'message' => $users
            );
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'No se encontraron usuarios',
            );
        }
    
        return response()->json($data,$data['code']);
    }

    //sube la imagen de perfil del usuario
    public function uploadcv(Request $request){

        //recoger datos 
        //la forma de llamarlo es asi porque ene front vamos
        //a usar una libreria que lo requiere
        $image = $request->file('file0');

        //validar imagen
        $validate = \Validator::make($request->all(), [
            //volver aqui para solucinar problema  validar imagen
            'file0'   => 'required'
        ]);

        //guardar imagen
        if(!$image || $validate->fails()){

            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'errores al subir el cv',
            );

        } else{
            $cv_name = time() . $image->getClientOriginalName();
            \Storage::disk('cv')->put($cv_name, \File::get($image));
            $data = array(
                'code' => 200,
                'status' => 'success',
                'message' => $cv_name,
            );

            $token = $request->header("Authorization");
            $jwtAuth = new \JwtAuth();
            $user = $jwtAuth->checkToken($token,true);
            $user_update = User::where('id', $user->sub)->update(['cv' => $cv_name]);

            
        }
        return response()->json($data,$data['code']);
        //return response($data,$data['code'])->header('Content-Type','text/plain');

    }

    //para hacer las busquedas de empresas: 
    public function empresasBusqueda(Request $request){
        $json = $request->input('json',null);

        $params_array = json_decode($json,true);            

        $validate = \Validator::make($params_array,[
            'sector' => 'required'
        ]);

        if($validate->fails()){
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error.'
            ];
        }else{
            $users = User::where('idrol', 2)->where('sector', $params_array['sector'])->get();
            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'Datos.',
                'total' => $users->count(),
                'result' => $users
            ];
        }
        return response()->json($data,$data['code']);
    }

    //para hacer las busquedas de empresas: 
    public function empresasBusquedaAvanzada(Request $request){
        $json = $request->input('json',null);

        $params_array = json_decode($json,true);            

        $validate = \Validator::make($params_array,[
            'sector' => 'required',
            'busqueda' => 'required'
        ]);

        if($validate->fails()){
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error.'
            ];
        }else{
            $users = User::where('idrol', 2)
              ->where('nombreEmpresa', 'LIKE', '%' . $params_array['busqueda'] . '%')
              ->where('sector', $params_array['sector'])
              ->get();
            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'Datos.',
                'total' => $users->count(),
                'result' => $users
            ];
        }
        return response()->json($data,$data['code']);
    }


    
    //sube la imagen de la publicacion que se esta haciendo
    public function uploadFotoPublicacion(Request $request){

        //recoger datos 
        //la forma de llamarlo es asi porque ene front vamos
        //a usar una libreria que lo requiere
        $image = $request->file('file0');

        //validar imagen
        $validate = \Validator::make($request->all(), [
            //volver aqui para solucinar problema  validar imagen
            'file0'   => 'required'
        ]);

        //guardar imagen
        if(!$image || $validate->fails()){

            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'errores',
            );

        } else{
            $foto_name = time() . $image->getClientOriginalName();
            \Storage::disk('publicaciones')->put($foto_name, \File::get($image));            

            $token = $request->header("Authorization");
            $jwtAuth = new \JwtAuth();
            $user = $jwtAuth->checkToken($token,true);

            $ultimoid = Publicar::where('user_id',$user->sub)->latest('id')->value('id');

            Publicar::where('id', $ultimoid)->update(['foto' => $foto_name]);

            $data = array(
                'code' => 200,
                'status' => 'success',
                'message' => $foto_name,
                'pruebas' => $ultimoid
            );

        }
        return response()->json($data,$data['code']);
        //return response($data,$data['code'])->header('Content-Type','text/plain');

    }

    public function getFoto($filename){
        $isset = \Storage::disk('publicaciones')->exists($filename);
        if($isset){
            $file = \Storage::disk('publicaciones')->get($filename);
            return new Response($file,200);
        }else{
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'la imagen no existe',
            );
        }
        return response()->json($data,$data['code']);
    }

    public function updatePasswd(Request $request){

        //comprobar que el usuario esta identificado
        $token = $request->header("Authorization");
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

        //obtener datos por post
        $json = $request->input('json',null);
        $params_array = json_decode($json,true);

        if($checkToken && !empty($params_array)){
            //actualizar el usuario
            //sacar usuario identificado
            $user = $jwtAuth->checkToken($token,true);
            //validar datos
            $validate = \Validator::make($params_array, [
                'password'   => 'required|alpha',
            ]);

            //actualizar usuario en la base de datos

            $pwd = hash('sha256',$params_array['password']);
            $user_update = User::where('id', $user->sub)->update(['password' => $pwd]);

            //devolver resultado
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $user,
                'changes' => $params_array,
            );


        }else{
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'El usuario no está identificado.',
            );
        }
        return response()->json($data,$data['code']);
    }

    public function infoPublica($id){
        $user = User::find($id);

        if(is_object($user)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'message' => $user
            );
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'el usuario no existe',
            );
        }

        return response()->json($data,$data['code']);
    }
}
