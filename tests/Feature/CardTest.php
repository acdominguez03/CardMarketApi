<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Collection;
use Tests\TestCase;

class CardTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_card() {

        $user = User::create([
            "username" => "andres",
            "email" => "andres@gmail.com",
            "password" => "$2y$10$4v5MUcNWS0ByjTRokicdde6wEWzdkatojGTt/4hxxSbSdcoHayW4S",
            "type" => "admin"
        ]);

        $collection = Collection::create([
            "name" => "OW2",
            "symbol" => "default.png",
            "releaseDate" => "2022-10-12"
        ]);

        $token = $user->createToken($user->username, [$user->type]);

        $response = $this->withHeaders(['Authorization'=>'Bearer '.$token->plainTextToken])->putJson('/api/cards/create', []);
 
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 500,
                'message' => 'No hay datos'
            ]);

        $response = $this->withHeaders(['Authorization'=>'Bearer '.$token->plainTextToken])->putJson('/api/cards/create', [
            "name" => "Tracer",
            "description" => "Good",
            "collection_id" =>  10
        ]);
    
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 404,
                'message' => 'Colección no encontrada'
            ]);

        $response = $this->withHeaders(['Authorization'=>'Bearer '.$token->plainTextToken])->putJson('/api/cards/create', [
            "name" => "Tracer",
            "description" => "Good",
            "collection_id" =>  1
        ]);
    
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 200,
                'message' => 'Carta añadida correctamente'
            ]);
    }
}
