<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trackinvoice extends Model
{
    use HasFactory;
    protected $table = 'trackinvoice';
    public $timestamps = true; // Ensure this property is set to true
    protected $fillable = ['bill_no', 'title', 'updated_by', 'notes'];

}
