<?php

use App\Http\Controllers\Api\v1\InvoiceController;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TesteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('v1')->group(function() {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function(){
        Route::get('/users/{user}', [UserController::class, 'show'])->middleware('ability:user-get');
        Route::get('/teste', [TesteController::class, 'index'])->middleware('ability:teste-index');
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    //possui middleware no InvoiceController
    Route::apiResource('invoices', InvoiceController::class);
    
    
});