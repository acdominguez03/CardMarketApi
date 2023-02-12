<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Helpers\ResponseGenerator;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function getAll(){
        
        $users = User::all();

        if($users){
            return ResponseGenerator::generateResponse("OK", 200, $users, "Usuarios obtenidos");
        }else{
            return ResponseGenerator::generateResponse("KO", 404, null, "No hay usuarios");
        }
    }

    public function register(Request $request){
        $json = $request->getContent();

        $data = json_decode($json);

        if($data){
             //validar datos
             $validate = Validator::make(json_decode($json,true), [
                'username' => 'required|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6|max:10',
                'type' => 'required|in:particular,profesional,admin'
            ]);
            if($validate->fails()){
                return ResponseGenerator::generateResponse("OK", 422, null, $validate->errors());
            }else{
                $user = new User();
                $user->username = $data->username;
                $user->email = $data->email;
                $user->password = Hash::make($data->password);
                $user->type = $data->type;

                try{
                    $user->save();
                    return ResponseGenerator::generateResponse("OK", 200, $user, "Usuario añadido correctamente");
                }catch(\Exception $e){
                    return ResponseGenerator::generateResponse("KO", 405, null, "Error al guardar");
                }
            }
        }
    }

    public function login(Request $request){
        $json = $request->getContent();

        $data = json_decode($json);

        if($data){
             //validar datos
            $validate = Validator::make(json_decode($json,true), [
                'username' => 'required',
                'password' => 'required|min:6|max:10'
            ]);
            if($validate->fails()){
                return ResponseGenerator::generateResponse("OK", 422, null, $validate->errors());
            }else{
                
                $user = User::where('username', 'like', $data->username)->get();

                if(!empty($user[0])){
                    try{
                        if(!Hash::check($data->password, $user[0]->password)) {
                            return ResponseGenerator::generateResponse("KO", 302, null, "Login incorrecto");
                        }else{
                            $user[0]->tokens()->delete();
        
                            $token = $user[0]->createToken($user[0]->username, [$user[0]->type]);
                            return ResponseGenerator::generateResponse("OK", 200, $token->plainTextToken, "Login correcto");
                        }
                    }catch(\Exception $e){
                        return ResponseGenerator::generateResponse("KO", 302, null, "Login incorrecto");
                    }
                }else{
                    return ResponseGenerator::generateResponse("KO", 404, null, "Usuario no encontrado");
                }

                
            }

            //lemkHUK3Qt68C4t6xtgDqvCUiRuOSUE2XaakGbI2
            //andres token: sZxym4aPuPQEMvCeYlSP5F08smCl2Pq0ZyQlWEC8


        }
    }

    public function recoverPassword(Request $request){
        $json = $request->getContent();

        $data = json_decode($json);

        if($data){
             //validar datos
            $validate = Validator::make(json_decode($json,true), [
                'email' => 'required|exists:users,email'
            ]);
            if($validate->fails()){
                return ResponseGenerator::generateResponse("OK", 422, null, $validate->errors());
            }else{
                $user = User::where('email', 'like', $data->email)->get();

                if(!empty($user)){
                    $newPass = Str::random(6);
                    $user[0]->password = Hash::make($newPass);

                    try{
                        $user[0]->save();
                        return ResponseGenerator::generateResponse("OK", 200, "Contraseña: " . $newPass, "Contraseña recuperada");
                    }catch(\Exception $e){
                        return ResponseGenerator::generateResponse("KO", 405,null, "Error al guardar el código del usuario");
                    }
                }else{
                    return ResponseGenerator::generateResponse("KO", 404, null, "Usuario con ese correo no encontrado");
                } 
            }

            //Ismael password : HgXbwL
        }
    }
}
