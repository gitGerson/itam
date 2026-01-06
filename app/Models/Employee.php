<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'name',
        'email',
        'phone',
        'department',
        'company',
        'group',
        'status',
        'synced_from_jpayroll',
        'last_synced_at',
    ];

    protected $casts = [
        'synced_from_jpayroll' => 'boolean',
        'last_synced_at' => 'datetime',
    ];
}
