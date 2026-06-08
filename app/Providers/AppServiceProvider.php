<?php

namespace App\Providers;

use App\Models\Bank;
use App\Models\ChequeDeposit;
use App\Models\CompanyLedger;
use App\Models\CreditnotesInvoice;
use App\Models\customerinfo;
use App\Models\customerledgerdetails;
use App\Models\daybook;
use App\Models\Expense;
use App\Models\invoice;
use App\Models\item;
use App\Models\pricelist;
use App\Models\purchaseorder;
use App\Observers\AuditLogObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        invoice::observe(AuditLogObserver::class);
        customerledgerdetails::observe(AuditLogObserver::class);
        customerinfo::observe(AuditLogObserver::class);
        item::observe(AuditLogObserver::class);
        CreditnotesInvoice::observe(AuditLogObserver::class);
        Bank::observe(AuditLogObserver::class);
        ChequeDeposit::observe(AuditLogObserver::class);
        CompanyLedger::observe(AuditLogObserver::class);
        Expense::observe(AuditLogObserver::class);
        daybook::observe(AuditLogObserver::class);
        pricelist::observe(AuditLogObserver::class);
        purchaseorder::observe(AuditLogObserver::class);
    }
}
