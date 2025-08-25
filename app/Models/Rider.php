<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rider extends Authenticatable
{
    use HasFactory, HasRoles;

    protected $fillable = [
        'full_name',
        'phone',
        'email',
        'password',
        'start_date',
        'status',
        'notes',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'string',
            'start_date' => 'datetime', // CORRECCIÓN: Agrega esta línea
        ];
    }

    // Nueva relación para los paquetes
    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }
}
