<?php

namespace App\Enums;

enum PackageStatus: string
{
    case RECEIVED = 'recibido';
    case ASSIGNED = 'asignado';
    case IN_TRANSIT = 'en_reparto';
    case DELIVERED = 'entregado';
    case INCIDENT = 'incidencia';
    case WAREHOUSE_RECEIVED = 'recibido_en_nave';
    case RETURNED_TO_ORIGIN = 'devuelto_a_origen';

    public function label(): string
    {
        return match ($this) {
            self::RECEIVED => 'Recibido',
            self::ASSIGNED => 'Asignado',
            self::IN_TRANSIT => 'En Reparto',
            self::DELIVERED => 'Entregado',
            self::INCIDENT => 'Incidencia',
            self::WAREHOUSE_RECEIVED => 'Recibido en Nave',
            self::RETURNED_TO_ORIGIN => 'Devuelto a Origen',
        };
    }
}
