<?php

namespace App\Http\Controllers;

use App\Support\FiscalNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FiscalDisplaySettingsController extends Controller
{
    public function update(Request $request)
    {
        abort_unless(auth()->check() && auth()->user()->isAdmin(), 403);

        Cache::forever(
            FiscalNumber::DISPLAY_MODE_CACHE_KEY,
            $request->boolean('enabled')
        );

        return back()->with(
            'status',
            $request->boolean('enabled')
                ? 'Fiscal numbers are now shown to users.'
                : 'Actual invoice and receipt numbers are now shown to users.'
        );
    }
}
