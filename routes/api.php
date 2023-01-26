<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CardsController;
use App\Http\Controllers\CollectionsController;

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
    Route::middleware(['auth:sanctum', 'abilities:admin'])->put('/addCardToCollection', [CardsController::class, 'addCardToCollection']);
    Route::middleware(['auth:sanctum', 'ability:profesional,particular'])->get('/searcher/{name}', [CardsController::class, 'searcher']);
    Route::middleware(['auth:sanctum', 'ability:profesional,particular'])->put('/sellCard', [CardsController::class, 'sellCard']);
    Route::get('/searchToBuy/{name}', [CardsController::class, 'searchToBuy']);

});

Route::prefix('collections')->group(function(){
    
    Route::middleware(['auth:sanctum', 'abilities:admin'])->put('/create', [CollectionsController::class, 'create']);

});

