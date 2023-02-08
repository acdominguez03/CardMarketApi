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
                'releaseDate' => 'required',
                'cards' => 'required|array|min:1',
                'cards.*.id' => 'nullable|integer|exists:cards,id',
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
                    $collection->releaseDate = $data->releaseDate;

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
                            $errors = [];

                            $card = new Card();
                            if(isset($cards->name)){
                                $card->name = $cards->name;

                                if(!is_string($cards->name)){
                                    array_push($errors, "El nombre tiene que ser un string");
                                }
                            }else{
                                array_push($errors, "Nombre de la carta no encontrado");
                            }

                            if(isset($cards->description)){
                                $card->description = $cards->description;
                                if(!is_string($cards->description)){
                                    array_push($errors, "La descripción tiene que ser un string");
                                }
                            }else{
                                array_push($errors, "Descripción de la carta no encontrado");
                            }

                            if(!empty($errors)){
                                return ResponseGenerator::generateResponse("OK", 200, $errors, "");
                            }

                            try{
                                $card->save();
                                $card->collections()->attach($collection->id);
                            }catch(\Exception $e){
                                $card->delete();
                                $collection->delete();
                            }
                        }

                    }
                    return ResponseGenerator::generateResponse("OK", 200, $collection, "Colección añadida correctamente");
                }
            }   
        }
    }

    public function update(Request $request){
        $json = $request->getContent();

        $data = json_decode($json);

        if($data){
            //validar datos
            $validate = Validator::make(json_decode($json,true), [
                'id' => 'required|exists:collections,id',
                'name' => 'required|string',
                'symbol' => 'required|string'
            ]);
            if($validate->fails()){
                return ResponseGenerator::generateResponse("KO", 422, null, $validate->errors());
            }else{
                $collection = Collection::find($data->id);

                $collection->name = $data->name;
                $collection->symbol = $data->symbol;

                try{
                    $collection->save();
                    return ResponseGenerator::generateResponse("OK", 200, null, "Colección editada");
                }catch(\Exception $e){
                    return ResponseGenerator::generateResponse("KO", 405, null, "Error al actualizar la colección");
                }
            }
        }else{
            return ResponseGenerator::generateResponse("KO", 404, null, "Faltan datos");
        }
    }
}
