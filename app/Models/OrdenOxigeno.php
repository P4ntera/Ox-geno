<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenOxigeno extends Model
{
    use HasFactory;

    protected $table = 'orden_oxigeno';
    protected $primaryKey = 'id_orden';
    public $timestamps = false;

    protected $fillable = [
        'id_paciente',
        'id_usuario',
        'v3',
        'relacion_ie',
        'fio2',
        'estado',
        'created_at',
        'updated_at',
    ];
    
    /* ================= RELACIONES ================= */

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
