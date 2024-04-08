<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trackinvoice extends Model
{
    use HasFactory;
    protected $table = 'trackinvoice';
    public $timestamps = true; // Ensure this property is set to true
    protected $fillable = ['title', 'updated_by', 'notes']; // Add 'title' to the $fillable array

}
