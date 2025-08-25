<?php
// File: app/Models/Incident.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'incident_type_id', // El ID del tipo de incidencia
        'notes',
        'reported_by_user_id'
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function incidentType(): BelongsTo
    {
        return $this->belongsTo(IncidentType::class);
    }

    public function reporter(): BelongsTo
    {
        // CORRECCIÓN: La relación debe apuntar a la tabla 'users'
        return $this->belongsTo(User::class, 'reported_by_user_id');
    }
}
