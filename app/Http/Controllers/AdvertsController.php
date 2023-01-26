<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Helpers\ResponseGenerator;
use Illuminate\Support\Facades\Validator;
use App\Models\Card;
use App\Models\Advert;

class AdvertsController extends Controller
{
    public function createAdvertToSellCard(Request $request){

        $json = $request->getContent();

        $data = json_decode($json);

        if($data){

            //validar datos
            $validate = Validator::make(json_decode($json,true), [
               'card_id' => 'required|integer',
               'nºcards' => 'required|integer',
               'price' => 'required|numeric'
            ]);
            if($validate->fails()){
                return ResponseGenerator::generateResponse("OK", 422, null, $validate->errors());
            }else{
                $card = Card::find($data->card_id);

                if($card){
                    $advert = new Advert();
                    $advert->card_id = $data->card_id;
                    $advert->nºcards = $data->nºcards;
                    $advert->price = $data->price;

                    try{
                        $advert->save();
                        return ResponseGenerator::generateResponse("OK", 200, null, "Anuncio creado y carta puesta a la venta");
                    }catch(\Exception $e){
                        return ResponseGenerator::generateResponse("KO", 405, null, "Error al guardar el anuncio");
                    }

                }else{
                    return ResponseGenerator::generateResponse("KO", 404, null, "Carta no encontrada");
                }
            }
        }else{
            return ResponseGenerator::generateResponse("KO", 500, null, "Datos no introducidos");
        }
    }
}
