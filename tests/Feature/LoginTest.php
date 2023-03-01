<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_login()
    {
        $user = User::create([
            "username" => "andres",
            "email" => "andres@gmail.com",
            "password" => "$2y$10$4v5MUcNWS0ByjTRokicdde6wEWzdkatojGTt/4hxxSbSdcoHayW4S",
            "type" => "admin"
        ]);

        $response = $this->postJson('/api/users/login', ['username' => 'Sally', 'password' => '123456']);
 
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 404,
                'message' => 'Usuario no encontrado'
            ]);

        $response = $this->postJson('/api/users/login', ['username' => 'Andres', 'password' => '1234567']);
 
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 302,
                'message' => 'Login incorrecto'
            ]);

        $response = $this->postJson('/api/users/login', ['username' => 'andres', 'password' => '123456']);
 
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 200,
                'message' => 'Login correcto'
            ]);
    }

    
}
