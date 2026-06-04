<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'customer_id',
        'phone_number',
        'message',
        'sms_type',
        'status',
        'api_response',
        'retry_count',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(invoice::class);
    }

    public function customer()
    {
        return $this->belongsTo(customerinfo::class);
    }

    /**
     * Check if SMS was successfully sent
     */
    public function isSent()
    {
        return $this->status === 'sent';
    }

    /**
     * Check if SMS sending failed
     */
    public function isFailed()
    {
        return $this->status === 'failed';
    }

    /**
     * Mark SMS as sent
     */
    public function markAsSent($apiResponse = null)
    {
        $this->update([
            'status' => 'sent',
            'api_response' => $apiResponse,
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark SMS as failed
     */
    public function markAsFailed($errorMessage = null)
    {
        $this->update([
            'status' => 'failed',
            'api_response' => $errorMessage,
        ]);
    }
}
