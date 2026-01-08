<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    protected $table = 'paciente';
    protected $primaryKey = 'id_paciente';
    public $timestamps = false;

    protected $fillable = [
        'nombre_paciente',
        'apellido_paciente',
        'cedula_paciente',
        'sexo_paciente',
        'edad_paciente',
        'especialidad',
        'ubicacion'
    ];
}
