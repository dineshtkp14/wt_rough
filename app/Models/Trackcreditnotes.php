<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trackcreditnotes extends Model
{
    use HasFactory;

    public $timestamps = true; // Ensure this property is set to true
    protected $fillable = ['Cn_bill_no','title', 'updated_by', 'notes']; // Add 'title' to the $fillable array

}
