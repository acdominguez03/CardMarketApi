<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Helpers\ResponseGenerator;

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
}
