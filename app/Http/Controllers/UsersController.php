<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Helpers\ResponseGenerator;
use App\Models\User;
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
}