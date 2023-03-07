<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Daybookcontroller;
use App\Http\Controllers\Bankcontroller;
use App\Http\Controllers\DistrinutorinfoController;
use App\Http\Controllers\Itemscontroller;
use App\Http\Controllers\CustomerinfoController;
use App\Http\Controllers\ItemsalesController;





Route::get('/', function () {
    return view('welcome');
});

Route::get('/itemsales',[ItemsalesController::class,'index'])->name('itemsales.index');
Route::get('/itemsales/create',[ItemsalesController::class,'create'])->name('itemsales.create');
Route::post('/itemsales',[ItemsalesController::class,'store'])->name('itemsales.store');


Route::get('/customerinfos',[CustomerinfoController::class,'index'])->name('customerinfos.index');
Route::get('/customerinfos/create',[CustomerinfoController::class,'create'])->name('customerinfos.create');
Route::post('/customerinfos',[CustomerinfoController::class,'store'])->name('customerinfos.store');
Route::get('/customerinfos/{customerinfo}/edit',[CustomerinfoController::class,'edit'])->name('customerinfos.edit');
Route::put('/customerinfos/{customerinfo}',[CustomerinfoController::class,'update'])->name('customerinfos.update');
Route::delete('/customerinfos/{customerinfo}',[CustomerinfoController::class,'destroy'])->name('customerinfos.destroy');


Route::get('/items',[Itemscontroller::class,'index'])->name('items.index');
Route::get('/items/create',[Itemscontroller::class,'create'])->name('items.create');
Route::post('/items',[Itemscontroller::class,'store'])->name('items.store');

Route::get('/disinfos',[DistrinutorinfoController::class,'index'])->name('disinfos.index');
Route::get('/disinfos/create',[DistrinutorinfoController::class,'create'])->name('disinfos.create');
Route::post('/disinfos',[DistrinutorinfoController::class,'store'])->name('disinfos.store');

Route::get('/daybooks',[Daybookcontroller::class,'index'])->name('daybooks.index');
Route::get('/daybooks/create',[Daybookcontroller::class,'create'])->name('daybooks.create');
Route::post('/daybooks',[Daybookcontroller::class,'store'])->name('daybooks.store');

Route::get('/banks',[Bankcontroller::class,'index'])->name('banks.index');
Route::get('/banks/create',[Bankcontroller::class,'create'])->name('banks.create');
Route::post('/banks',[Bankcontroller::class,'store'])->name('banks.store');
