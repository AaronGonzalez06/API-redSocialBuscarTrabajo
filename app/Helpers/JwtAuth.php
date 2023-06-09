<?php

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth{

    public $key;

    public function __construct(){
        $this->key = "clave_secreta_aaron";
    }

    public function signup($email,$password, $getToken = null){
        $user = User::where([
            'email' => $email,
            'password' => $password
        ])->first();

        $signup= false;
        if(is_object($user)){
            $signup = true;
        }

        if($signup){
            $token = array(
                'sub' => $user->id,
                'email' => $user->email,
                'image' => $user->image,
                'idrol' => $user->idrol,
                'cv' => $user->cv,
                'name' => $user->name,
                'surname' => $user->surname,
                'edad' => $user->edad,
                'telefono' => $user->telefono,
                'DNI' => $user->DNI,
                'provincia' => $user->provincia,
                'password' => $user->password,
                'image' => $user->image,
                'CIF' => $user->CIF,
                'nombreEmpresa' => $user->nombreEmpresa,
                'codigoPostal' => $user->codigoPostal,
                'localidad' => $user->localidad,
                'sector' => $user->sector,
                'iat' => time(),
                'exp' => time() + (7 * 24 * 24 * 60 * 60)
            );

            $jwt = JWT::encode($token,$this->key,'HS256');
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
            if(is_null($getToken)){
                $data = $jwt;
            }else{
                $data = $decoded;
            }

        }else {
            $data = array(
                'status' => 'error',
                'message' => 'Login incorrecto'
            );
        }

        return $data;
    }

    public function checkToken($jwt,$getIdentity = false){
        $auth = false;

        try{
            $jwt = str_replace('"','', $jwt);
            $decoded = JWT::decode($jwt,$this->key, ['HS256']);
        }catch(\UnexpectedValueException $e){
            $auth = false;
        }catch(\DomainException $e){
            $auth = false;
        }

        if(!empty($decoded) && is_object($decoded) && isset($decoded->sub)){
            $auth = true;
        }else{
            $auth = false;
        }

        if($getIdentity){
            return $decoded;
        }
        
        return $auth;
    }

}