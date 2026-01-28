<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ars extends Model
{
    protected $table = 'ars';
    protected $primaryKey = 'id_ars';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'precio_litro',
    ];

    
}
