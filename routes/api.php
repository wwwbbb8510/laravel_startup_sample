<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ItemController;
use App\Models\User;

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

Route::post('/tokens/create', function (Request $request) {
    //validate fields in request
    $request->validate([
        'email' => 'required|string|email|max:255',
        'password' => 'required|string|min:8',
        'token_name' => 'required|string',
    ]);

    //generate token
    $credentials = $request->only('email', 'password');
    if(!Auth::once($credentials)){
        return ['error' => sprintf('user email and password do not match!')];
    }
    $token = Auth::user()->createToken($request->token_name);

    return ['token' => $token->plainTextToken, 'token_type' => 'Bearer'];
});

Route::apiResource('items', ItemController::class)->middleware('auth:sanctum');
