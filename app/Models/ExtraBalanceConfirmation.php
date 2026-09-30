<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraBalanceConfirmation extends Model
{
    protected $table = 'extra_balance_confirmations';
    protected $guarded = ['id'];
    protected $casts = ['firm_id' => 'integer'];
    public function firm() { return $this->belongsTo(VatFirm::class); }
}
