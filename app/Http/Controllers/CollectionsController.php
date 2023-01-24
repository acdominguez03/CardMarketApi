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
               'cards' => 'required|array:name,description'
            ]);
            if($validate->fails()){
                return ResponseGenerator::generateResponse("OK", 422, null, $validate->errors());
            }else{
                $collection = new Collection();
                $collection->name = $data->name;
                $collection->symbol = $data->symbol;
                $collection->editDate = $data->editDate;

                foreach($data->cards as $cards){
                    $card = new Card();
                    $card->name = $cards->name;
                    $card->description = $cards->description;

                    $card->collections()->attach($collection->id);
                }

                try{
                    $collection->save();
                    return ResponseGenerator::generateResponse("OK", 200, $collection, "Usuario añadido correctamente");
                }catch(\Exception $e){
                    return ResponseGenerator::generateResponse("KO", 405, null, "Error al guardar");
                }
                
            }
        }
    }
}
