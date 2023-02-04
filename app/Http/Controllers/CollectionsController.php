<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Helpers\ResponseGenerator;
use Illuminate\Support\Facades\Validator;
use App\Models\Collection;
use App\Models\Card;
use Illuminate\Support\Facades\Http;


class CollectionsController extends Controller
{
    public function create(Request $request){
        $json = $request->getContent();

        $data = json_decode($json);

        if($data){
            //validar datos
            $validate = Validator::make(json_decode($json,true), [
                'name' => 'required',
                'symbol' => 'required',
                'editDate' => 'required',
                'cards' => 'required|array|min:1',
                'cards.*.name' => 'required|string|max:255',
                'cards.*.description' => 'required|string|max:255',
                'cards.*.id' => 'nullable|integer',
            ]);
            if($validate->fails()){
                return ResponseGenerator::generateResponse("KO", 422, null, $validate->errors());
            }else{
                if(empty($data->cards)){
                    return ResponseGenerator::generateResponse("OK", 422, null, "Por favor para crear una colección es necesario añadir cartas"); 
                }else{
                    $collection = new Collection();
                    $collection->name = $data->name;
                    $collection->symbol = $data->symbol;
                    $collection->editDate = $data->editDate;

                    try{
                        $collection->save();
                    }catch(\Exception $e){
                        return ResponseGenerator::generateResponse("KO", 405, null, "Error al guardar");
                    }

                    foreach($data->cards as $cards){
                        if(isset($cards->id)){
                            $existCard = Card::find($cards->id);

                            try{
                                $existCard->collections()->attach($collection->id);
                            }catch(\Exception $e){
                                $collection->delete();
                            }
                        }else{

                            $card = new Card();
                            $card->name = $cards->name;
                            $card->description = $cards->description;

                            try{
                                $card->save();
                                $card->collections()->attach($collection->id);
                            }catch(\Exception $e){
                                $collection->delete();
                            }
                        }

                    }
                    return ResponseGenerator::generateResponse("OK", 200, $collection, "Colección añadida correctamente");
                }
            }   
        }
    }

    public function getCollectionsFromDB(){
        $response = Http::get('https://api.magicthegathering.io/v1/sets')->body();

        $data = json_decode($response);

        if($data){
            foreach($data->sets as $set){

                $collection = Collection::firstOrCreate(
                    ['code' => $set->code],
                    ['name' => $set->name, 'symbol' => 'black', 'releaseDate' => $set->releaseDate,]
                );

                if($collection) {
                    $collection->code = $set->code;
                    $collection->name = $set->name;
                    $collection->symbol = "black";
                    $collection->releaseDate = $set->releaseDate;
                    try{
                        $collection->save();
                    }catch(\Exception $e){
                        return ResponseGenerator::generateResponse("OK", 405, $e, "Error al actualizar la colección"); 
                    }
                    
                }
            }
            
            return ResponseGenerator::generateResponse("OK", 200, null, "Colecciones guardadas");
        }
    }
}
