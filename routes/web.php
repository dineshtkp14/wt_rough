<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Daybookcontroller;
use App\Http\Controllers\Bankcontroller;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\Itemscontroller;
use App\Http\Controllers\CustomerinfoController;

use App\Http\Controllers\CustomerLedgerDetailsController;
use App\Http\Controllers\CustomerLedgerHistroy;
use App\Http\Controllers\CustomerPdfGenerator;
use App\Http\Controllers\ItemsalesController;
use App\Http\Controllers\PricelistController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/itemsales',[ItemsalesController::class,'index'])->name('itemsales.index');
Route::get('/itemsales/create',[ItemsalesController::class,'create'])->name('itemsales.create');
Route::get('/exam_manage_ajax',[ItemsalesController::class,'examshow_ajax'])->name('itemsales.ajax');

Route::post('/itemsales',[ItemsalesController::class,'store'])->name('itemsales.store');


Route::get('/pricelists',[PricelistController::class,'index'])->name('pricelists.index');
Route::get('/pricelists/create',[PricelistController::class,'create'])->name('pricelists.create');
Route::post('/pricelists',[PricelistController::class,'store'])->name('pricelists.store');
Route::get('/pricelists/{pricelist}/edit',[PricelistController::class,'edit'])->name('pricelists.edit');
Route::put('/pricelists/{pricelists}',[PricelistController::class,'update'])->name('pricelists.update');
Route::delete('/pricelists/{customerinfo}',[PricelistController::class,'destroy'])->name('pricelists.destroy');



Route::get('/customerinfos',[CustomerinfoController::class,'index'])->name('customerinfos.index');
Route::get('/customerinfos/create',[CustomerinfoController::class,'create'])->name('customerinfos.create');
Route::post('/customerinfos',[CustomerinfoController::class,'store'])->name('customerinfos.store');
Route::get('/customerinfos/{customerinfo}/edit',[CustomerinfoController::class,'edit'])->name('customerinfos.edit');
Route::put('/customerinfos/{customerinfo}',[CustomerinfoController::class,'update'])->name('customerinfos.update');
Route::delete('/customerinfos/{customerinfo}',[CustomerinfoController::class,'destroy'])->name('customerinfos.destroy');



// //forhtmltopdf
Route::get('/pdf/view',[CustomerPdfGenerator::class,'pdfview'])->name('pdf.view');
Route::get('/pdf/convert',[CustomerPdfGenerator::class,'pdfgenerate'])->name('pdf.convert');




Route::get('/items',[Itemscontroller::class,'index'])->name('items.index');
Route::get('/items/create',[Itemscontroller::class,'create'])->name('items.create');
Route::post('/items',[Itemscontroller::class,'store'])->name('items.store');

Route::get('/companys',[CompanyController::class,'index'])->name('companys.index');
Route::get('/companys/create',[CompanyController::class,'create'])->name('companys.create');
Route::post('/companys',[CompanyController::class,'store'])->name('companys.store');
Route::get('/companys/{companys}/edit',[CompanyController::class,'edit'])->name('companys.edit');
Route::put('/companys/{companys}',[CompanyController::class,'update'])->name('companys.update');
Route::delete('/companys/{companys}',[CompanyController::class,'destroy'])->name('companys.destroy');


Route::get('/daybooks',[Daybookcontroller::class,'index'])->name('daybooks.index');
Route::get('/daybooks/create',[Daybookcontroller::class,'create'])->name('daybooks.create');
Route::post('/daybooks',[Daybookcontroller::class,'store'])->name('daybooks.store');

Route::get('/banks',[Bankcontroller::class,'index'])->name('banks.index');
Route::get('/banks/create',[Bankcontroller::class,'create'])->name('banks.create');
Route::post('/banks',[Bankcontroller::class,'store'])->name('banks.store');
Route::get('/banks/pdf/convert/',[Bankcontroller::class,'show_intopdfbankdetails'])->name('banks.convert');



Route::get('/cpayments',[CustomerLedgerDetailsController::class,'index'])->name('cpayments.index');
Route::get('/cpayments/create',[CustomerLedgerDetailsController::class,'create'])->name('cpayments.create');
Route::post('/cpayments',[CustomerLedgerDetailsController::class,'store'])->name('cpayments.store');

//Route::get('/clhs',[CustomerLedgerHistroy::class,'index'])->name('clhs.index');
Route::get('/clhs',[CustomerLedgerHistroy::class,'returnchoosendatehistroy'])->name('clhs.returnchoosendatehistroy');
Route::get('clhs/pdf/convert/',[CustomerLedgerHistroy::class,'PdfGenerateCustomerDetails'])->name('clhspdf.convert');


Route::get('/billno',[CustomerLedgerHistroy::class,'returnBillsDEtailsByInvoiceid'])->name('customer.billno');
Route::get('/billno/pdf/convert/',[CustomerLedgerHistroy::class,'showPDF_InvoiveBillByBillno'])->name('invoicebillno.convert');




Route::get('/cbills',[CustomerLedgerHistroy::class,'returncusbills'])->name('cbills.returncusbills');









