<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CentroSalud extends Model
{
    protected $table = 'centro_salud';
    protected $primaryKey = 'id_centro';
    public $timestamps = false;

    protected $fillable = [
        'nombre_centro',
        'direccion_centro',
        'ciudad_centro',
        'telefono_centro',
        'pisos',
        'habitaciones',
        'pago_litro'
    ];
}
