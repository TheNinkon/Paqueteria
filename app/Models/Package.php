<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'unique_code',
        'shipment_id',
        'client_id',
        'rider_id',
        'status',
    ];

    /**
     * Get the client that owns the package.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the rider that owns the package.
     */
    public function rider()
    {
        return $this->belongsTo(Rider::class);
    }

    /**
     * Get the histories for the package.
     * Esta es la relación que faltaba.
     */
    public function histories(): HasMany
    {
        return $this->hasMany(PackageHistory::class);
    }
}
