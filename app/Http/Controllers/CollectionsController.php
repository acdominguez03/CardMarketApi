<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Helpers\ResponseGenerator;
use Illuminate\Support\Facades\Validator;
use App\Models\Collection;
use App\Models\Card;

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
               'cards' => 'required|array|min:1'
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
                            if(!empty($existCard)){
                                $existCard->collection_id = $collection->id;
                            }
                            try{
                                $existCard->save();
                            }catch(\Exception $e){
                                print($e);
                            }
                        }else{

                            $card = new Card();
                            $card->name = $cards->name;
                            $card->description = $cards->description;
                            $card->collection_id = $collection->id;

                            try{
                                $card->save();
                                $card->collections()->attach($collection->id);
                            }catch(\Exception $e){
                                print($e);
                            }
                        }

                    }
                    return ResponseGenerator::generateResponse("OK", 200, $collection, "Colección añadida correctamente");
                }
            }   
        }
    }
}
