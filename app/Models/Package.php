<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Package extends Model
{
    use HasFactory;
    protected $fillable = ['unique_code', 'shipment_id', 'status', 'client_id'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
