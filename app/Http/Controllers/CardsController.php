<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Helpers\ResponseGenerator;
use Illuminate\Support\Facades\Validator;
use App\Models\Card;
use App\Models\Collection;

class CardsController extends Controller
{
    public function create(Request $request){
        $json = $request->getContent();

        $data = json_decode($json);

        if($data){
            //validar datos
            $validate = Validator::make(json_decode($json,true), [
               'name' => 'required',
               'description' => 'required',
               'collection' => 'required|integer'
            ]);
            if($validate->fails()){
                return ResponseGenerator::generateResponse("OK", 422, null, $validate->errors());
            }else{
                $collection = Collection::first();

                if(!empty($collection)){
                    $card = new Card();
                    $card->name = $data->name;
                    $card->description = $data->description;
                    $card->collection = $data->collection;

                    try{
                        $card->save();
                        return ResponseGenerator::generateResponse("OK", 200, $card, "Usuario añadido correctamente");
                    }catch(\Exception $e){
                        return ResponseGenerator::generateResponse("KO", 405, null, "Error al guardar");
                    }
                }else{
                    return ResponseGenerator::generateResponse("KO", 405, null, "Por favor crea primero una colección");
                }
                
            }
        }
    }
}
