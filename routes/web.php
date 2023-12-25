<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Daybookcontroller;
use App\Http\Controllers\Bankcontroller;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyLedger;
use App\Http\Controllers\CompanyLedgerBillEntryController;
use App\Http\Controllers\CompanyLedgerController;
use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\Itemscontroller;
use App\Http\Controllers\CustomerinfoController;
use App\Http\Controllers\ProfitController;
use App\Http\Controllers\Invoicecontroller;
use App\Http\Controllers\TotalSalesController;






use App\Http\Controllers\CustomerLedgerDetailsController;
use App\Http\Controllers\CustomerLedgerHistroy;
use App\Http\Controllers\CustomerPdfGenerator;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemsalesController;
use App\Http\Controllers\openingbalanceController;
use App\Http\Controllers\PricelistController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;


Route::get('/', function () {
    return view('login');
});

//Route::get('/',[PricelistController::class,'index'])->name('home');

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


Route::get('/stocks',[StockController::class,'index'])->name('stocks.index');
// Route::get('/openingbalance/create',[openingbalanceController::class,'create'])->name('opeaning.create');
// Route::get('/openingbalance',[openingbalanceController::class,'store'])->name('opeaning.store');

Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard.index');





Route::get('/customerinfos',[CustomerinfoController::class,'index'])->name('customerinfos.index');
Route::get('/customerinfos/create',[CustomerinfoController::class,'create'])->name('customerinfos.create');
Route::post('/customerinfos',[CustomerinfoController::class,'store'])->name('customerinfos.store');
Route::get('/customerinfos/{customerinfo}/edit',[CustomerinfoController::class,'edit'])->name('customerinfos.edit');
Route::put('/customerinfos/{customerinfo}',[CustomerinfoController::class,'update'])->name('customerinfos.update');
Route::delete('/customerinfos/{customerinfo}',[CustomerinfoController::class,'destroy'])->name('customerinfos.destroy');


//opeaningbalance

Route::get('/openingbalances',[openingbalanceController::class,'index'])->name('openingbalances.index');
Route::get('/openingbalances/create',[openingbalanceController::class,'create'])->name('openingbalances.create');
Route::post('/openingbalances',[openingbalanceController::class,'store'])->name('openingbalances.store');
Route::get('/openingbalances/{customerinfo}/edit',[openingbalanceController::class,'edit'])->name('openingbalances.edit');
Route::put('/openingbalances/{customerinfo}',[openingbalanceController::class,'update'])->name('openingbalances.update');
Route::delete('/openingbalances/{customerinfo}',[openingbalanceController::class,'destroy'])->name('openingbalances.destroy');




// //forhtmltopdf
Route::get('/pdf/view',[CustomerPdfGenerator::class,'pdfview'])->name('pdf.view');
Route::get('/pdf/convert',[CustomerPdfGenerator::class,'pdfgenerate'])->name('pdf.convert');




Route::get('/items',[Itemscontroller::class,'index'])->name('items.index');
Route::get('/items/create',[Itemscontroller::class,'create'])->name('items.create');
Route::post('/items',[Itemscontroller::class,'store'])->name('items.store');
Route::get('/items/{items}/edit',[Itemscontroller::class,'edit'])->name('items.edit');
Route::put('/items/{items}',[Itemscontroller::class,'update'])->name('items.update');
Route::delete('/items/{items}',[Itemscontroller::class,'destroy'])->name('items.destroy');

Route::get('/companys',[CompanyController::class,'index'])->name('companys.index');
Route::get('/companys/create',[CompanyController::class,'create'])->name('companys.create');
Route::post('/companys',[CompanyController::class,'store'])->name('companys.store');
Route::get('/companys/{companys}/edit',[CompanyController::class,'edit'])->name('companys.edit');
Route::put('/companys/{companys}',[CompanyController::class,'update'])->name('companys.update');
Route::delete('/companys/{companys}',[CompanyController::class,'destroy'])->name('companys.destroy');


Route::get('/daybooks',[Daybookcontroller::class,'index'])->name('daybooks.index');
Route::get('/daybooks/create',[Daybookcontroller::class,'create'])->name('daybooks.create');
Route::post('/daybooks',[Daybookcontroller::class,'store'])->name('daybooks.store');
Route::get('/daybooks/{daybooks}/edit',[Daybookcontroller::class,'edit'])->name('daybooks.edit');
Route::put('/daybooks/{daybooks}',[Daybookcontroller::class,'update'])->name('daybooks.update');
Route::delete('/daybooks/{daybooks}',[Daybookcontroller::class,'destroy'])->name('daybooks.destroy');

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

//companyledgerdetails withpdfconverter
Route::get('/companyledgerdetails',[CompanyLedgerBillEntryController::class,'returnchoosendatehistroy'])->name('companyledgerdetails.returnchoosendatehistroy');
Route::get('companyledgerdetails/pdf/convert/',[CompanyLedgerBillEntryController::class,'PdfGenerateCustomerDetails'])->name('companyledgerdetails.convert');


Route::get('/billno',[CustomerLedgerHistroy::class,'returnBillsDEtailsByInvoiceid'])->name('customer.billno');
Route::get('/billno/pdf/convert/',[CustomerLedgerHistroy::class,'showPDF_InvoiveBillByBillno'])->name('invoicebillno.convert');




Route::get('/cbills',[CustomerLedgerHistroy::class,'returncusbills'])->name('cbills.returncusbills');


Route::get('/companyLedgers',[CompanyLedgerController::class,'index'])->name('companyLedgers.index');
Route::get('/companyLedgers/create',[CompanyLedgerController::class,'create'])->name('companyLedgers.create');
Route::post('/companyLedgers',[CompanyLedgerController::class,'store'])->name('companyLedgers.store');
Route::get('/companyLedgers/{companyLedgers}/edit',[CompanyLedgerController::class,'edit'])->name('companyLedgers.edit');
Route::put('/companyLedgers/{companyLedgers}',[CompanyLedgerController::class,'update'])->name('companyLedgers.update');
Route::delete('/companyLedgers/{companyLedgers}',[CompanyLedgerController::class,'destroy'])->name('companyLedgers.destroy');



Route::get('/companybillentry',[CompanyLedgerBillEntryController::class,'index'])->name('companybillentry.index');
Route::get('/companybillentry/create',[CompanyLedgerBillEntryController::class,'create'])->name('companybillentry.create');
Route::post('/companybillentry',[CompanyLedgerBillEntryController::class,'store'])->name('companybillentry.store');
Route::get('/companybillentry/{companybillentry}/edit',[CompanyLedgerBillEntryController::class,'edit'])->name('companybillentry.edit');
Route::put('/companybillentry/{companybillentry}',[CompanyLedgerBillEntryController::class,'update'])->name('companybillentry.update');
Route::delete('/companybillentry/{companybillentry}',[CompanyLedgerBillEntryController::class,'destroy'])->name('companybillentry.destroy');



// Route::get('/profit', 'ProfitController@index')->name('profit.index');

Route::get('profit', [ProfitController::class, 'index'])->name('profit');

Route::get('/invoice',[Invoicecontroller::class,'index'])->name('invoice.index');
// Route::get('/invoice/create',[Bankcontroller::class,'create'])->name('banks.create');
// Route::post('/banks',[Bankcontroller::class,'store'])->name('banks.store');
// Route::get('/banks/pdf/convert/',[Bankcontroller::class,'show_intopdfbankdetails'])->name('banks.convert');

 
Route::get('/totalsales', [TotalSalesController::class, 'index'])->name('totalsales.index');


Route::get('/allsalesdetails', [CustomerLedgerDetailsController::class, 'showdetails'])->name('allsalesdetails.showdetails');



Route::get('/showsalesperday', [Invoicecontroller::class, 'showonlysalesperday'])->name('showonlysalesperday');





 Route::get('/dashboard', [CustomAuthController::class, 'dashboard']); 
 Route::get('login', [CustomAuthController::class, 'index'])->name('login');
 Route::post('postlogin', [CustomAuthController::class, 'login'])->name('postlogin'); 
 Route::get('signup', [CustomAuthController::class, 'signup'])->name('register-user');
 Route::get('changepass', [CustomAuthController::class, 'changepass'])->name('user.password');
 Route::get('/change-password', [CustomAuthController::class, 'changePassword'])->name('change-password');
 Route::post('/change-password', [CustomAuthController::class, 'updatePassword'])->name('update-password');
 
 
 
 Route::post('postsignup', [CustomAuthController::class, 'signupsave'])->name('postsignup'); 
 Route::get('signout', [CustomAuthController::class, 'signOut'])->name('signout');


