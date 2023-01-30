<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Helpers\ResponseGenerator;
use Illuminate\Support\Facades\Validator;
use App\Models\Card;
use App\Models\User;
use App\Models\Collection;
use App\Models\Advert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CardsController extends Controller
{
    public function create(Request $request){
        $json = $request->getContent();

        $data = json_decode($json);

        if($data){
            //validar datos
            $validate = Validator::make(json_decode($json,true), [
               'name' => 'required',
               'description' => 'required'
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

                    try{
                        $card->save();
                    }catch(\Exception $e){
                        return ResponseGenerator::generateResponse("KO", 405, null, "Error al guardar");
                    }

                    if($checkCollection){
                        try{
                            $card->collections()->attach($checkCollection);
                            return ResponseGenerator::generateResponse("OK", 200, $card, "Carta añadida correctamente");
                        }catch(\Expection $e){
                            $card->delete();
                            return ResponseGenerator::generateResponse("KO", 404, null, "Error al ligar la carta a la colección");
                        }
                    }else{
                        return ResponseGenerator::generateResponse("KO", 404, null, "Colección no encontrada");
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

    public function searcher(Request $request){

        $json = $request->getContent();

        $data = json_decode($json);

        if($data){
            Log::info('Obtenemos el valor de la petición', ['data' => $data]);

            $validate = Validator::make(json_decode($json,true), [
               'name' => 'required'
            ]);

            if($validate->fails()){
                Log::error('Error en la validación de los datos', ['errors' => $validate->errors()]);
                return ResponseGenerator::generateResponse("OK", 422, null, $validate->errors());
            }else{
                Log::info('Datos validados correctamente', ['valor' => $data->name]);

                try {
                    $cards = Card::select('id', 'name')->where('name', 'LIKE', "%". $data->name . "%")->get();
                    Log::info('Obtenemos las cartas por el filtro', ['cartas' => $cards]);
                    return ResponseGenerator::generateResponse("OK", 200, $cards, "Cartas filtradas");
                }catch(\Exception $e){
                    Log::error('Error en la base de datos', ['error' => $e]);
                    return ResponseGenerator::generateResponse("OK", 200, $e, "Error en la base de datos");
                }
            }
        }else{
            Log::error('No hay filtro');
            return ResponseGenerator::generateResponse("OK", 500, null,"No se ha encontrado el filtro");
        }
        
    }

    public function searchToBuy(Request $request){
        $json = $request->getContent();

        $data = json_decode($json);

        if($data){
            //validar datos
            $validate = Validator::make(json_decode($json,true), [
               'name' => 'required|string'
            ]);
            if($validate->fails()){
                return ResponseGenerator::generateResponse("OK", 422, null, $validate->errors());
            }else{
                $cards = DB::table('cards')
                ->join('adverts', 'cards.id', '=', 'adverts.card_id')
                ->select('cards.id', 'cards.name', 'adverts.price')
                ->where('name', 'LIKE', "%". $data->name . "%", 'AND', 'cards.id', 'IN', 'adverts.card_id')
                ->orderBy('adverts.price','DESC')
                ->get();

                return ResponseGenerator::generateResponse("OK", 200, $cards, "Cartas filtradas");
            }
        }else{
            return ResponseGenerator::generateResponse("KO", 500, null, "Faltan el nombre por el que buscar");
        }   
    }
}
