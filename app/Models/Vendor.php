<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    use HasFactory, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'contact_person',
        'manager_id', // ¡Asegúrate de que este campo está aquí!
    ];

    /**
     * Define la relación con el Gerente que supervisa a este proveedor.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Define la relación con sus repartidores.
     */
    public function riders(): HasMany
    {
        return $this->hasMany(Rider::class);
    }
}
