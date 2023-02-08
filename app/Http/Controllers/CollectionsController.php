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

    public function getDataFromDatabase(){
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
                    $collection->symbol = "default.png";
                    $collection->releaseDate = $set->releaseDate;
                    try{
                        $collection->save();
                    }catch(\Exception $e){
                        return ResponseGenerator::generateResponse("OK", 405, $e, "Error al actualizar la colección"); 
                    }
                    
                }
            }
            
        }

        $response = Http::get('https://api.magicthegathering.io/v1/cards')->body();

        $data = json_decode($response);

        if($data){
            foreach($data->cards as $card){

                $getCard = Card::where('number','LIKE',$card->number)->get();

                if(count($getCard) == 0){
                    $newCard = new Card();
                    $newCard->number = $card->number;
                    $newCard->name = $card->name;
                    $newCard->description = $card->text;
    
                    try {
                        $newCard->save();
                        $collection = Collection::where('code','LIKE',$card->set)->get();
                        $newCard->collections()->attach($collection[0]->id); 
                    }catch(\Exception $e){
                        $newCard->delete();
                        return ResponseGenerator::generateResponse("KO", 405, $e,"Error al crear la carta");
                    }
                }else{
                    $getCard[0]->number = $card->number;
                    $getCard[0]->name = $card->name;
                    $getCard[0]->description = $card->text;
                    try{
                        $getCard[0]->save();
                    }catch(\Exception $e){
                        return ResponseGenerator::generateResponse("OK", 405, $e, "Error al actualizar la carta"); 
                    }
                }
            }
            
            return ResponseGenerator::generateResponse("OK", 200, null, "Cartas y colecciones guardadas");
        }
    }
}
