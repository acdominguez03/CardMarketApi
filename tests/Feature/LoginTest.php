<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_not_found()
    {
        $response = $this->postJson('/api/users/login', ['username' => 'Sally', 'password' => '123456']);
 
        $response
            ->assertStatus(200)
            ->assertJson([
                'code' => 404,
                'message' => 'Usuario no encontrado'
            ]);
    }

    // public function test_incorrect_credentials()
    // {
    //     $response = $this->postJson('/api/users/login', ['username' => 'Andres', 'password' => '1234567']);
 
    //     $response
    //         ->assertStatus(200)
    //         ->assertJson([
    //             'code' => 302,
    //             'message' => 'Login incorrecto'
    //         ]);
    // }

    // public function test_correct_login()
    // {
    //     $response = $this->postJson('/api/users/login', ['username' => 'Andres', 'password' => '123456']);
 
    //     $response
    //         ->assertStatus(200)
    //         ->assertJson([
    //             'code' => 200,
    //             'message' => 'Login correcto'
    //         ]);
    // }
}
