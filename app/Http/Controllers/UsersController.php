<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Helpers\ResponseGenerator;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;

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
                try{
                    $user = User::where('username', 'like', $data->username)->firstOrFail();

                    if(!Hash::check($data->password, $user->password)) {
                        return ResponseGenerator::generateResponse("KO", 404, null, "Login incorrecto, comprueba la contraseña");
                    }else{
                        
                        $credentials = $request->only('username', 'password');
                        $token = JWTAuth::attempt($credentials);
                        return ResponseGenerator::generateResponse("OK", 200, $token, "Login correcto");
                    }
                }catch(\Exception $e){
                    return ResponseGenerator::generateResponse("KO", 404, null, "Login incorrecto, usuario erróneo");
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

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function googleCallback()
    {
        // try {
      
        $user_google = Socialite::driver('google')->stateless()->user();

        $user = User::updateOrCreate([
            'google_id' => $user_google->id,    
        ], [
            'username' => $user_google->name,
            'email' => $user_google->email,
            'password' => Hash::make("123456")
        ]);

        $user->tokens()->delete();
    
         $token = $user->createToken($user->username);
        return ResponseGenerator::generateResponse("OK", 200, $token->plainTextToken, "Login correcto");
    }
}
