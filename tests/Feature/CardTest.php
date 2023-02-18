<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CardTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_empty_value()
    {
        $token = "rgEJ2ntphmvxWc0tv0Xulxx2edztyDDXmrjmm8F8";
        $response = $this->withHeaders(['Authorization'=>'Bearer '.$token])->putJson('/api/cards/create', []);
 
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 500,
                'message' => 'No hay datos'
            ]);
    }

    public function test_error_collection()
    {
        $token = "rgEJ2ntphmvxWc0tv0Xulxx2edztyDDXmrjmm8F8";
        $response = $this->withHeaders(['Authorization'=>'Bearer '.$token])->putJson('/api/cards/create', [
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
    }

    public function test_create_card()
    {
        $token = "rgEJ2ntphmvxWc0tv0Xulxx2edztyDDXmrjmm8F8";
        $response = $this->withHeaders(['Authorization'=>'Bearer '.$token])->putJson('/api/cards/create', [
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
