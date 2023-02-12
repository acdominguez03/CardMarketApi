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
        $token = "Y6ErhhwxQHG1JGVTHQZnQLEBEIgXitEgKKrdhgPI";
        $response = $this->withHeaders(['Authorization'=>'Bearer '.$token, 'Accept' => 'application/json'])->postJson('/api/cards/create');
 
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 404,
                'message' => 'No hay datos'
            ]);
    }
}
