<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tarea extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'titulo',
        'autor_id',
        'asignado_id',
        'cuerpo',
        'fecha_expiracion',
        'categorias',
        'comentarios'
    ];

    protected $casts = [
        'categorias' => 'array',
        'comentarios' => 'array',
        'fecha_expiracion' => 'date'
    ];
}
