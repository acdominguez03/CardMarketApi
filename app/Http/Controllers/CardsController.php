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
               'collection_id' => 'required|integer'
            ]);
            if($validate->fails()){
                return ResponseGenerator::generateResponse("OK", 422, null, $validate->errors());
            }else{
                $collection = Collection::first();

                if(!empty($collection)){
                    $card = new Card();
                    $card->name = $data->name;
                    $card->description = $data->description;
                    $checkCollection = Collection::where('id', '=', $data->collection_id)->first();
                    if($checkCollection){
                        $card->collection_id = $data->collection_id;
                    }else{
                        return ResponseGenerator::generateResponse("KO", 404, null, "Colección no encontrada");
                    }

                    try{
                        $card->save();
                        return ResponseGenerator::generateResponse("OK", 200, $card, "Carta añadida correctamente");
                    }catch(\Exception $e){
                        return ResponseGenerator::generateResponse("KO", 405, null, "Error al guardar");
                    }
                }else{
                    return ResponseGenerator::generateResponse("KO", 405, null, "Por favor crea primero una colección");
                }
                
            }
        }
    }

    public function addCardToCollection(Request $request){
        $json = $request->getContent();

        $data = json_decode($json);

        if($data){
            //validar datos
            $validate = Validator::make(json_decode($json,true), [
               'card_id' => 'required|integer',
               'collection_id' => 'required|integer'
            ]);
            if($validate->fails()){
                return ResponseGenerator::generateResponse("OK", 422, null, $validate->errors());
            }else{
                $card = Card::find($data->card_id);
                $collection = Collection::find($data->collection_id);

                if(empty($card)){
                    return ResponseGenerator::generateResponse("OK", 404, null,"Carta no encontrada");
                }else if(empty($collection)){
                    return ResponseGenerator::generateResponse("OK", 404, null,"Colección no encontrada");
                }else{
                    try{
                        $card->collections()->attach($data->collection_id);
                        return ResponseGenerator::generateResponse("OK", 200, null,"Carta añadida a la colección");
                    }catch(\Exception $e){
                        return ResponseGenerator::generateResponse("OK", 405, null,"Error al añadir la carta a la colección");
                    }
                }
            }
        }
    }

    public function searcher($name){
        $cards = Card::select('id', 'name')->where('name', 'LIKE', "%". $name . "%")->get();

        return ResponseGenerator::generateResponse("OK", 200, $cards, "Cartas filtradas");
    }

    public function sellCard(){

    }

    public function searchToBuy($name){
        
    }
}
