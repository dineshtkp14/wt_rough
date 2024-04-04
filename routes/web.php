<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Daybookcontroller;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyLedger;
use App\Http\Controllers\CompanyLedgerBillEntryController;
use App\Http\Controllers\CompanyLedgerController;
use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\Itemscontroller;
use App\Http\Controllers\CustomerinfoController;
use App\Http\Controllers\CreditnotesInvoices_controller;
use App\Http\Controllers\ProfitController;
use App\Http\Controllers\Invoicecontroller;
use App\Http\Controllers\TotalSalesController;
use App\Http\Controllers\Creditnotes_controller;
use App\Http\Controllers\showperday_controller;
use App\Http\Controllers\Purchse_controller;
use App\Http\Controllers\Employee_controller;
use App\Http\Controllers\ViewwholeitembillController;
use App\Http\Controllers\AdminstockController;
use App\Http\Controllers\TrackitemstableController;
use App\Http\Controllers\MyfirmController;
use App\Http\Controllers\TransferGoodsController;
use App\Http\Controllers\TrackcompanyledgerController;
use App\Http\Controllers\CashReceiptController;
use App\Http\Controllers\ChequeDepositController;





















use App\Http\Controllers\BankDeposit_CounterCheckController;
use App\Http\Controllers\CustomerLedgerDetailsController;

use App\Http\Controllers\UserdashboardController;

use App\Http\Controllers\trackinvoiceController;

use App\Http\Controllers\CustomerLedgerHistroy;
use App\Http\Controllers\CustomerPdfGenerator;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemsalesController;
use App\Http\Controllers\openingbalanceController;
use App\Http\Controllers\PricelistController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ExpensesController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\trackCreditnotesController;



Route::get('/', function () {
    return view('login');
});


//onlyredirctuserpages

Route::get('/itemdash',[UserdashboardController::class,'itemdash'])->name('itemdash');
Route::get('/daybookdash',[UserdashboardController::class,'daybookdash'])->name('daybookdash');
Route::get('/companydash',[UserdashboardController::class,'companydash'])->name('companydash');
Route::get('/purchaseorderdash',[UserdashboardController::class,'purchaseorderdash'])->name('purchaseorderdash');
Route::get('/customerdash',[UserdashboardController::class,'customerdash'])->name('customerdash');
Route::get('/bankdash',[UserdashboardController::class,'bankdash'])->name('bankdash');
Route::get('/invoicedash',[UserdashboardController::class,'invoicedash'])->name('invoicedash');
Route::get('/cndash',[UserdashboardController::class,'cndash'])->name('cndash');

Route::get('/userdash',[UserdashboardController::class,'index'])->name('userdash');



Route::get('/itemsales',[ItemsalesController::class,'index'])->name('itemsales.index');
Route::get('/itemsales/create',[ItemsalesController::class,'create'])->name('itemsales.create');
Route::get('/itemsales/{itemsales}/edit',[ItemsalesController::class,'edit'])->name('itemsales.edit');
Route::put('/itemsales/{itemsales}',[ItemsalesController::class,'update'])->name('itemsales.update');
Route::post('/itemsales',[ItemsalesController::class,'store'])->name('itemsales.store');


// Route::get('/creditnotes/create', [Creditnotes_controller::class, 'showCreditNotesPage'])->name('showCreditNotesPage.check');


Route::get('/creditnotes',[Creditnotes_controller::class,'index'])->name('creditnotes.index');
Route::get('/creditnotes/create',[Creditnotes_controller::class,'create'])->name('creditnotes.create');
Route::get('/creditnotes/{creditnotes}/edit',[Creditnotes_controller::class,'edit'])->name('creditnotes.edit');
Route::put('/creditnotes/{creditnotes}',[Creditnotes_controller::class,'update'])->name('creditnotes.update');
Route::post('/creditnotes',[Creditnotes_controller::class,'store'])->name('creditnotes.store');



Route::get('/creditnotesbillnoonlyview',[Creditnotes_controller::class,'returnBillsDEtailsByInvoiceidforviewingcreditnotebillonlyview'])->name('creditnotescustomeronlyview.billno');


Route::get('/creditnotesbillno',[Creditnotes_controller::class,'returnBillsDEtailsByInvoiceidforviewingcreditnotebill'])->name('creditnotescustomer.billno');
Route::delete('/creditnotesbillno', [Creditnotes_controller::class, 'deletebillfromdatabaseforcreditnotes'])->name('creditnotescustomers.deletebillno');
Route::put('/creditnotesbillno', [Creditnotes_controller::class, 'updateinvoiicetypeforcreditnotes'])->name('creditnotescustomers.updatebillinvoicetype');

//updatecredtnotes customername
Route::put('/creditnotesbillno/updatecusnameCN', [Creditnotes_controller::class, 'updatecustomernameCN'])->name('updatecustomernameCN');



Route::get('/creditnotesbillno/pdf/convert/',[Creditnotes_controller::class,'PDF_returnBillsDEtailsByInvoiceidforviewingcreditnotebill'])->name('creditnotesbillno.convert');


Route::get('/deletedcnbillno',[Creditnotes_controller::class,'returndeletedcnBillsDEtailsByInvoiceid'])->name('deletedcncustomer.deletebillno');


Route::get('/cninvoice',[CreditnotesInvoices_controller::class,'index'])->name('cninvoice.index');
Route::get('/cninvoice/{cninvoice}/edit',[CreditnotesInvoices_controller::class,'edit'])->name('cninvoice.edit');
Route::put('/cninvoice/{cninvoice}',[CreditnotesInvoices_controller::class,'update'])->name('cninvoice.update');

Route::get('/deletedCNinvoice',[CreditnotesInvoices_controller::class,'returndeletedcninvoice'])->name('deletedcn.invoice');



Route::get('/pricelists',[PricelistController::class,'index'])->name('pricelists.index');
Route::get('/pricelists/create',[PricelistController::class,'create'])->name('pricelists.create');
Route::post('/pricelists',[PricelistController::class,'store'])->name('pricelists.store');
Route::get('/pricelists/{pricelist}/edit',[PricelistController::class,'edit'])->name('pricelists.edit');
Route::put('/pricelists/{pricelists}',[PricelistController::class,'update'])->name('pricelists.update');
Route::delete('/pricelists/{customerinfo}',[PricelistController::class,'destroy'])->name('pricelists.destroy');

Route::get('/expenses',[ExpensesController::class,'index'])->name('expenses.index');
Route::get('/expenses/create',[ExpensesController::class,'create'])->name('expenses.create');
Route::post('/expenses',[ExpensesController::class,'store'])->name('expenses.store');
Route::get('/expenses/{pricelist}/edit',[ExpensesController::class,'edit'])->name('expenses.edit');
Route::put('/expenses/{expenses}',[ExpensesController::class,'update'])->name('expenses.update');
Route::delete('/expenses/{customerinfo}',[ExpensesController::class,'destroy'])->name('expenses.destroy');

//searchbydate totalsum
Route::get('/expenses/search', [ExpensesController::class, 'search'])->name('expenses.search');

Route::get('/stocks',[StockController::class,'index'])->name('stocks.index');
// Route::get('/stocks',[StockController::class,'filterStocks'])->name('stocks.filterfirm');
Route::get('/stocks/filter', [StockController::class, 'filterStocks'])->name('stocks.filterfirm');

Route::put('/stocks/{id}', [StockController::class, 'update'])->name('stockpriceupdate');


//checkandrmeoveoutofstock
Route::post('/stocks',[StockController::class,'updateofs'])->name('stocks.updateofs');


//adminstockcontroller
Route::get('/adminstocks',[AdminstockController::class,'index'])->name('adminstocks.index');

Route::get('adminstocks/pdf/convert',[AdminstockController::class,'adminstockpdfgenerate'])->name('adminstock.convert');





// Route::get('/openingbalance/create',[openingbalanceController::class,'create'])->name('opeaning.create');
// Route::get('/openingbalance',[openingbalanceController::class,'store'])->name('opeaning.store');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');





Route::get('/customerinfos',[CustomerinfoController::class,'index'])->name('customerinfos.index');
Route::get('/customerinfos/create',[CustomerinfoController::class,'create'])->name('customerinfos.create');
Route::post('/customerinfos',[CustomerinfoController::class,'store'])->name('customerinfos.store');
Route::get('/customerinfos/{customerinfo}/edit',[CustomerinfoController::class,'edit'])->name('customerinfos.edit');
Route::put('/customerinfos/{customerinfo}',[CustomerinfoController::class,'update'])->name('customerinfos.update');
Route::delete('/customerinfos/{customerinfo}',[CustomerinfoController::class,'destroy'])->name('customerinfos.destroy');

//myfirmcontroller
Route::get('/myfirm',[MyfirmController::class,'index'])->name('myfirm.index');
Route::get('/myfirm/create',[MyfirmController::class,'create'])->name('myfirm.create');
Route::post('/myfirm',[MyfirmController::class,'store'])->name('myfirm.store');
Route::get('/myfirm/{myfirm}/edit',[MyfirmController::class,'edit'])->name('myfirm.edit');
Route::put('/myfirm/{myfirm}',[MyfirmController::class,'update'])->name('myfirm.update');
Route::delete('/myfirm/{myfirm}',[MyfirmController::class,'destroy'])->name('myfirm.destroy');


//myfirmcontroller
Route::get('/transfergoods',[TransferGoodsController::class,'index'])->name('transfergoods.index');
Route::get('/transfergoods/create',[TransferGoodsController::class,'create'])->name('transfergoods.create');
Route::post('/transfergoods',[TransferGoodsController::class,'store'])->name('transfergoods.store');
Route::get('/transfergoods/{transfergoods}/edit',[TransferGoodsController::class,'edit'])->name('transfergoods.edit');
Route::put('/transfergoods/{transfergoods}',[TransferGoodsController::class,'update'])->name('transfergoods.update');
Route::delete('/transfergoods/{transfergoods}',[TransferGoodsController::class,'destroy'])->name('transfergoods.destroy');




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


//chequedeposit
Route::get('/chequedeposit',[ChequeDepositController::class,'index'])->name('chequedeposit.index');
Route::get('/chequedeposit/create',[ChequeDepositController::class,'create'])->name('chequedeposit.create');
Route::post('/chequedeposit',[ChequeDepositController::class,'store'])->name('chequedeposit.store');
Route::get('/chequedeposit/{chequedeposit}/edit',[ChequeDepositController::class,'edit'])->name('chequedeposit.edit');
Route::put('/chequedeposit/{chequedeposit}',[ChequeDepositController::class,'update'])->name('chequedeposit.update');
Route::delete('/chequedeposit/{chequedeposit}',[ChequeDepositController::class,'destroy'])->name('chequedeposit.destroy');

//chequeReceipt
Route::get('/chequereceipt',[ChequeDepositController::class,'returnReceiptDeyailsbyReceiptNo'])->name('chequereceipt.search');
Route::get('chequereceipt/pdf/convert/',[ChequeDepositController::class,'returnReceiptDeyailsbyReceiptNoPDF'])->name('chequereceipt.convert');



Route::get('/items',[Itemscontroller::class,'index'])->name('items.index');
Route::get('/items/create',[Itemscontroller::class,'create'])->name('items.create');
Route::post('/items',[Itemscontroller::class,'store'])->name('items.store');
Route::get('/items/{items}/edit',[Itemscontroller::class,'edit'])->name('items.edit');
Route::put('/items/{items}',[Itemscontroller::class,'update'])->name('items.update');
Route::delete('/items/{items}',[Itemscontroller::class,'destroy'])->name('items.destroy');

//itemviewwholebilllist
Route::get('/wholebilllist', [ViewwholeitembillController::class, 'returnWholebillitems'])->name('ViewWholeitemsBill.index');
Route::get('/wholebilllist/pdf/convert/',[ViewwholeitembillController::class,'PDF_returnWholebillitems'])->name('wholebilllist.convert');

// Route::get('/wholebilllist-page', [ViewWholeitemsBill::class, 'showwholebilllistpage'])->name('ViewWholeitemsBill.page');

Route::get('/companys',[CompanyController::class,'index'])->name('companys.index');
Route::get('/companys/create',[CompanyController::class,'create'])->name('companys.create');
Route::post('/companys',[CompanyController::class,'store'])->name('companys.store');
Route::get('/companys/{companys}/edit',[CompanyController::class,'edit'])->name('companys.edit');
Route::put('/companys/{companys}',[CompanyController::class,'update'])->name('companys.update');
Route::delete('/companys/{companys}',[CompanyController::class,'destroy'])->name('companys.destroy');


    Route::get('/employees',[Employee_controller::class,'index'])->name('employees.index');
    Route::get('/employees/{employees}/edit',[Employee_controller::class,'edit'])->name('employees.edit');
    Route::put('/employees/{employees}',[Employee_controller::class,'update'])->name('employees.update');
    Route::delete('/employees/{employees}',[Employee_controller::class,'destroy'])->name('employees.destroy');


Route::get('/purorder',[Purchse_controller::class,'index'])->name('purorder.index');
Route::get('/purorder/create',[Purchse_controller::class,'create'])->name('purorder.create');
Route::post('/purorder',[Purchse_controller::class,'store'])->name('purorder.store');
Route::get('/purorder/{purorder}/edit',[Purchse_controller::class,'edit'])->name('purorder.edit');
Route::put('/purorder/{purorder}',[Purchse_controller::class,'update'])->name('purorder.update');
Route::delete('/purorder/{purorder}',[Purchse_controller::class,'destroy'])->name('purorder.destroy');

Route::get('/daybooks',[Daybookcontroller::class,'index'])->name('daybooks.index');
Route::get('/daybooks/create',[Daybookcontroller::class,'create'])->name('daybooks.create');
Route::post('/daybooks',[Daybookcontroller::class,'store'])->name('daybooks.store');
Route::get('/daybooks/{daybooks}/edit',[Daybookcontroller::class,'edit'])->name('daybooks.edit');
Route::put('/daybooks/{daybooks}',[Daybookcontroller::class,'update'])->name('daybooks.update');
Route::delete('/daybooks/{daybooks}',[Daybookcontroller::class,'destroy'])->name('daybooks.destroy');

Route::get('/banks',[BankController::class,'index'])->name('banks.index');
Route::get('/banks/create',[BankController::class,'create'])->name('banks.create');
Route::post('/banks',[BankController::class,'store'])->name('banks.store');
Route::get('/banks/pdf/convert/',[BankController::class,'show_intopdfbankdetails'])->name('banks.convert');



Route::get('/cpayments',[CustomerLedgerDetailsController::class,'index'])->name('cpayments.index');
Route::get('/cpayments/create',[CustomerLedgerDetailsController::class,'create'])->name('cpayments.create');
Route::post('/cpayments',[CustomerLedgerDetailsController::class,'store'])->name('cpayments.store');
Route::get('/cpayments/{cpayments}/edit',[CustomerLedgerDetailsController::class,'edit'])->name('cpayments.edit');
Route::put('/cpayments/{cpayments}',[CustomerLedgerDetailsController::class,'update'])->name('cpayments.update');
Route::delete('/cpayments/{cpayments}',[CustomerLedgerDetailsController::class,'destroy'])->name('cpayments.destroy');

//Route::get('/clhs',[CustomerLedgerHistroy::class,'index'])->name('clhs.index');
Route::get('/clhs',[CustomerLedgerHistroy::class,'returnchoosendatehistroy'])->name('clhs.returnchoosendatehistroy');
Route::get('/allcashandcredit',[CustomerLedgerHistroy::class,'returnchoosendatehistroycashandcredit'])->name('returnchoosendatehistroycashandcredit');

Route::get('clhs/pdf/convert/',[CustomerLedgerHistroy::class,'PdfGenerateCustomerDetails'])->name('clhspdf.convert');
Route::get('allcashandcredit/pdf/convert/',[CustomerLedgerHistroy::class,'pdfreturnchoosendatehistroycashandcredit'])->name('pdfreturnchoosendatehistroycashandcredit.convert');



//cashReceipt
Route::get('/cashreceipt',[CashReceiptController::class,'returnReceiptDeyailsbyReceiptNo'])->name('cashreceipt.search');
Route::get('cashreceipt/pdf/convert/',[CashReceiptController::class,'returnReceiptDeyailsbyReceiptNoPDF'])->name('cashreceipt.convert');





//companyledgerdetails withpdfconverter
Route::get('/companyledgerdetails',[CompanyLedgerBillEntryController::class,'returnchoosendatehistroy'])->name('companyledgerdetails.returnchoosendatehistroy');
Route::get('companyledgerdetails/pdf/convert/',[CompanyLedgerBillEntryController::class,'PdfGenerateCustomerDetails'])->name('companyledgerdetails.convert');


Route::get('/deletedbillno',[CustomerLedgerHistroy::class,'returndeletedBillsDEtailsByInvoiceid'])->name('deletedcustomer.deletebillno');
Route::get('/deletedinvoice',[CustomerLedgerHistroy::class,'returndeletedinvoice'])->name('deleted.invoice');

Route::get('/billno',[CustomerLedgerHistroy::class,'returnBillsDEtailsByInvoiceid'])->name('customer.billno');
Route::delete('/billno', [CustomerLedgerHistroy::class, 'deletebillfromdatabase'])->name('customer.deletebillno');
Route::put('/billno', [CustomerLedgerHistroy::class, 'updateinvoiicetype'])->name('customer.updatebillinvoicetype');
Route::put('/billno/updatecusname', [CustomerLedgerHistroy::class, 'updatecustomername'])->name('updatecustomername');

Route::get('/onlyviewbill',[CustomerLedgerHistroy::class,'onlyviewbillafterbill'])->name('onlyviewbillafterbill');

Route::get('/billno/pdf/convert/',[CustomerLedgerHistroy::class,'showPDF_InvoiveBillByBillno'])->name('invoicebillno.convert');

//trackinvoice
Route::get('/trackinvoice',[trackinvoiceController::class,'index'])->name('trackinvoice.index');
Route::get('/trackcreditnotes',[trackCreditnotesController::class,'index'])->name('trackcreditnotes.index');

//trackitemstable
Route::get('/trackitemstable',[TrackitemstableController::class,'index'])->name('trackitemstable.index');

Route::get('/trackcompanyledger',[TrackcompanyledgerController::class,'index'])->name('TrackcompanyledgerController.index');


Route::get('/cbills',[CustomerLedgerHistroy::class,'returncusbills'])->name('cbills.returncusbills');





Route::get('/companyLedgerspay',[CompanyLedgerController::class,'index'])->name('companyLedgerspay.index');
Route::get('/companyLedgerspay/create',[CompanyLedgerController::class,'create'])->name('companyLedgerspay.create');
Route::post('/companyLedgerspay',[CompanyLedgerController::class,'store'])->name('companyLedgerspay.store');
Route::get('/companyLedgerspay/{companyLedgerspay}/edit',[CompanyLedgerController::class,'edit'])->name('companyLedgerspay.edit');
Route::put('/companyLedgerspay/{companyLedgerspay}',[CompanyLedgerController::class,'update'])->name('companyLedgerspay.update');
Route::delete('/companyLedgerspay/{companyLedgerspay}',[CompanyLedgerController::class,'destroy'])->name('companyLedgerspay.destroy');




Route::get('/companybillentry',[CompanyLedgerBillEntryController::class,'index'])->name('companybillentry.index');
Route::get('/companybillentry/create',[CompanyLedgerBillEntryController::class,'create'])->name('companybillentry.create');
Route::post('/companybillentry',[CompanyLedgerBillEntryController::class,'store'])->name('companybillentry.store');
Route::get('/companybillentry/{companybillentry}/edit',[CompanyLedgerBillEntryController::class,'edit'])->name('companybillentry.edit');
Route::put('/companybillentry/{companybillentry}',[CompanyLedgerBillEntryController::class,'update'])->name('companybillentry.update');
Route::delete('/companybillentry/{companybillentry}',[CompanyLedgerBillEntryController::class,'destroy'])->name('companybillentry.destroy');


// Route::get('/profit', 'ProfitController@index')->name('profit.index');

Route::get('profit', [ProfitController::class, 'index'])->name('profit');

Route::get('/invoice',[Invoicecontroller::class,'index'])->name('invoice.index');
Route::get('/invoice/{invoice}/edit',[Invoicecontroller::class,'edit'])->name('invoice.edit');
Route::put('/invoice/{invoice}',[Invoicecontroller::class,'update'])->name('invoice.update');

 
Route::get('/totalsales', [TotalSalesController::class, 'index'])->name('totalsales.index');


Route::get('/allsalesdetails', [CustomerLedgerDetailsController::class, 'showdetails'])->name('allsalesdetails.showdetails');
Route::get('/accl', [CustomerLedgerDetailsController::class, 'showallcuscreditdetails'])->name('allsalesdetails.showallcuscreditdetails');
Route::get('/accl/pdf/convert', [CustomerLedgerDetailsController::class, 'showallcuscreditdetails'])->name('accl.convert');


//counterandbankcheckcash
Route::get('/CheckBankDeposit/show', [BankDeposit_CounterCheckController::class, 'showBankdeposit_UpdateForm'])->name('CheckBankDeposit.index');
Route::put('/CheckBankDeposit/update', [BankDeposit_CounterCheckController::class, 'BankDeposit_UpdateForm'])->name('CheckBankDeposit.update');

Route::get('/CheckCounterDeposit/show', [BankDeposit_CounterCheckController::class, 'showCounterdeposit_UpdateForm'])->name('CheckCounterDeposit.index');
Route::put('/CheckCounterDeposit/update', [BankDeposit_CounterCheckController::class, 'CounterDeposit_UpdateForm'])->name('CheckCounterDeposit.update');




Route::get('/showsalesperday', [showperday_controller::class, 'showonlysalesperday'])->name('showonlysalesperday.pp');
Route::get('/onetable_showsalesperda', [showperday_controller::class, 'showonlysalesperdayinone_table'])->name('showonlysalesperdayinone_table.pp');





//  Route::get('/dashboard', [CustomAuthController::class, 'dashboard']); 
 Route::get('login', [CustomAuthController::class, 'index'])->name('login');
 Route::post('postlogin', [CustomAuthController::class, 'login'])->name('postlogin'); 
 Route::get('signup', [CustomAuthController::class, 'signup'])->name('register-user');

//  Route::get('changepass', [CustomAuthController::class, 'changepass'])->name('user.password');
 Route::get('/change-password', [CustomAuthController::class, 'changePassword'])->name('change-password');
 Route::post('/change-password', [CustomAuthController::class, 'updatePassword'])->name('update-password');
 
 
 
 Route::post('postsignup', [CustomAuthController::class, 'signupsave'])->name('postsignup'); 
 Route::get('signout', [CustomAuthController::class, 'signOut'])->name('signout');


