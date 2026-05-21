<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoriasModel extends Model
{
    protected $table = 'historias';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'titulo',
        'descripcion',
        'responsable',
        'estado',
        'puntos',
        'fecha_creacion',
        'fecha_finalizacion',
        'sprint_id'
    ];
}