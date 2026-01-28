<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoConsumoDetalle extends Model
{
    protected $table = 'estado_consumo_detalle';

    public $timestamps = false;
    protected $fillable = [
        'id_estado_consumo',
        'id_consumo',
        'costo_real',
        'costo_ars',
        'cubierto_ars',
        'porcentaje_ars',
        'costo_final',
    ];

    public function consumo()
    {
        return $this->belongsTo(ConsumoOxigeno::class, 'id_consumo');
    }

    public function estadoConsumo()
    {
        return $this->belongsTo(EstadoConsumo::class, 'id_estado_consumo'); 
    }
}
