<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\PackageStatus;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        // ... otros campos
        'status',
    ];

    protected $casts = [
        'status' => PackageStatus::class,
    ];

    public function history()
    {
        return $this->hasMany(PackageHistory::class);
    }
    public function client()
{
    return $this->belongsTo(Client::class);
}
}
