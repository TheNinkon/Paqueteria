<?php
// File: app/Models/PackageHistory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageHistory extends Model
{
    use HasFactory;

    protected $fillable = ['package_id', 'user_id', 'status', 'description'];

    // CORRECCIÓN: Definimos la relación con el paquete
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    // CORRECCIÓN: Definimos la relación con el usuario (quien reporta)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
