<?php

use App\Http\Controllers\companyNameSearchAPI;
use App\Http\Controllers\CustomerAPI;
use App\Http\Controllers\ItemsSearchAPI;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/customer_search/{name}',[CustomerAPI::class,'index']);
Route::get('/items_search/{name}',[ItemsSearchAPI::class,'index']);
Route::get('/company_search/{name}',[companyNameSearchAPI::class,'index']);





