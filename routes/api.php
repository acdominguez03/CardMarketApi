<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CardsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('users')->group(function(){
    
    Route::get('/getAll', [UsersController::class, 'getAll']);
    Route::put('/register', [UsersController::class, 'register']);
    Route::post('/login', [UsersController::class, 'login']);
    Route::post('/recoverPassword', [UsersController::class, 'recoverPassword']);

});

Route::prefix('cards')->group(function(){
    
    Route::middleware(['auth:sanctum', 'abilities:admin'])->put('/create', [CardsController::class, 'create']);

});

