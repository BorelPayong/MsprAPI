<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::get('login', 'AuthController@login');
Route::post('login','AuthController@login');
Route::post('register','AuthController@register');
Route::post('qr-login', 'AuthController@qrLogin');


Route::middleware('auth:sanctum')->group( function () {
    Route::get('products', 'ProductsController@index');
    Route::get('orders/{user_id}','OrdersController@index');
});
