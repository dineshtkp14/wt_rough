<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupSalesItem extends Model
{
    use HasFactory;
     protected $table = 'backup_salesitems'; // Set the table name for the backup sales items
 protected $fillable = [
        'id',
        'invoiceid',
        'itemid',
        'unstockedname',
        'quantity',
        'price',
        'discount',
        'subtotal',
    ];
  
}
