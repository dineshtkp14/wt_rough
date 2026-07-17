<?php

namespace Tests\Feature;

use App\Http\Controllers\CustomerVatSaleController;
use App\Models\Myfirm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class VatStockSeparationTest extends TestCase
{
    public function test_vat_stock_uses_its_own_table_and_foreign_key(): void
    {
        $this->assertTrue(Schema::hasTable('vat_stock_items'));
        $this->assertTrue(Schema::hasColumn('customer_vat_sale_items', 'vat_stock_item_id'));
        $this->assertFalse(Schema::hasColumn('customer_vat_sale_items', 'item_id'));
        $this->assertSame(0, DB::table('customer_vat_sale_items')->whereNull('vat_stock_item_id')->count());
    }

    public function test_independent_vat_stock_management_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('customer-vat-sales.stock'));
        $this->assertTrue(Route::has('customer-vat-sales.stock.create'));
        $this->assertTrue(Route::has('customer-vat-sales.stock.store'));
        $this->assertTrue(Route::has('customer-vat-sales.stock.edit'));
        $this->assertTrue(Route::has('customer-vat-sales.stock.update'));
        $this->assertTrue(Route::has('customer-vat-sales.stock.destroy'));
        $this->assertTrue(Route::has('customer-vat-sales.monthly-book'));
    }

    public function test_nepali_monthly_vat_book_renders_for_a_vat_firm(): void
    {
        $firm = Myfirm::query()->where('nick_name', 'Durga')->firstOrFail();
        $request = Request::create('/customer-vat-sales/monthly-book', 'GET', [
            'myfirm_id' => $firm->id,
            'bs_year' => 2083,
            'bs_month' => 3,
        ]);

        $view = app(CustomerVatSaleController::class)->monthlyBook($request);

        $this->assertSame('customer-vat-sales.monthly-book', $view->name());
        $this->assertCount(12, $view->getData()['monthlySummary']);
    }
}
