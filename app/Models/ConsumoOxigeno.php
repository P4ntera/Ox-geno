<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumoOxigeno extends Model
{
    protected $table = 'consumo_oxigeno';
    protected $primaryKey = 'id_consumo';
    public $timestamps = false;

    protected $fillable = [
        'id_orden',
        'id_habitacion',
        'fecha_inicio',
        'fecha_fin',
        'tiempo_total',
        'volumen_total_litros',
        'estado_clinico',
        'flujo_lpm',
        'id_usuario',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'volumen_total_litros' => 'decimal:2',
        'costo_total' => 'decimal:2',
    ];

    /* ================= RELACIONES ================= */

    public function orden()
    {
        return $this->belongsTo(OrdenOxigeno::class, 'id_orden');
    }

    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class, 'id_habitacion');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    /* ================= HELPERS ================= */

    public function getEstadoAttribute()
    {
        return $this->fecha_fin === null ? 'En Proceso' : 'Finalizado';
    }
    public function estadoDetalle()
    {
        return $this->hasOne(EstadoConsumoDetalle::class, 'id_consumo', 'id_consumo');
    }

}
