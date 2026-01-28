<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Prompts\Key;

class Paciente extends Model
{
    protected $table = 'paciente';
    protected $primaryKey = 'id_paciente';
    public $timestamps = false;

    protected $fillable = [
        'id_ars',
        'nombre_paciente',
        'apellido_paciente',
        'cedula_paciente',
        'sexo_paciente',
        'edad_paciente',
        'especialidad',
        'ubicacion'
    ];

    public function ordenes()
    {
        return $this->hasMany(OrdenOxigeno::class, 'id_paciente', 'id_paciente');
    }

    public function consumoOxigeno()
    {
        return $this->hasMany(ConsumoOxigeno::class, 'id_paciente', 'id_paciente');
    }
    
    public function centrosalud()
    {
        return $this->belongsTo(CentroSalud::class, 'id_centro_salud', 'id_centro_salud');
    }
    public function ars()
    {
        return $this->belongsTo(Ars::class, 'id_ars', 'id_ars');
    }
}
