<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Habitacion extends Model
{
    protected $table = 'habitaciones';
    protected $primaryKey = 'id_habitacion';
    public $timestamps = false;

    protected $fillable = [
        'id_centro',
        'piso',
        'numero_habitacion',
    ];

    public function centro()
    {
        return $this->belongsTo(CentroSalud::class, 'id_centro');
    }
}
