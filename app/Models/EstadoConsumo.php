<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoConsumo extends Model
{
    protected $table = 'estado_consumo';
    protected $primaryKey = 'id_estado_consumo';

    public $timestamps = false;

    protected $fillable = [
        'id_paciente',
        'fecha_cierre',
        'total_litros',
        'total_tiempo',
        'total_costo',
        'total_ars',
        'total_final',
        'total_cubierto',
        'precio_base_ars',
        'precio_base_centro',
        'id_usuario',
    ];

    public function detalles()
    {
        return $this->hasMany(EstadoConsumoDetalle::class, 'id_estado_consumo');
    }
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
