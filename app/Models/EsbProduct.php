<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EsbProduct extends Model
{
    use HasFactory;

    protected $table = 'esb_products';

    protected $fillable = [
        'product_id',
        'product_code',
        'product_name',
        'category_id',
        'category_name',
        'sub_category_id',
        'sub_category_name',
        'bill_of_material_id',
        'bill_of_material_code',
        'bill_of_material_name',
        'requestable',
        'purchasable',
        'vat',
        'receipt_tolerance',
        'saleable',
        'notes',
        'created_date',
        'created_by',
        'edited_date',
        'edited_by',
        'product_details',
        'last_synced_at',
    ];

    protected $casts = [
        'product_details' => 'array',
        'last_synced_at' => 'datetime',
    ];
}
