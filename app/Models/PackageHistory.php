<?php
// File: app/Models/PackageHistory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'status',
        'details', // Corregido: 'description' -> 'details'
        'user_id',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
