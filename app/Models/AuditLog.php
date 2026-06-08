<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'auditable_type',
        'auditable_id',
        'event',
        'title',
        'old_values',
        'new_values',
        'url',
        'ip_address',
        'user_agent',
        'user_id',
        'user_name',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];
}
