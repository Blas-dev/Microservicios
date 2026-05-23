<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoriasModel extends Model
{
    // Indicamos el nombre exacto de la tabla en la base de datos
    protected $table = 'historias';

    // Clave primaria
    protected $primaryKey = 'id';

    // Campos que permitimos guardar o actualizar masivamente
    protected $fillable = [
        'titulo',
        'descripcion',
        'responsable',
        'estado', // 'nueva', 'activa', 'finalizada', 'impedimento'
        'puntos',
        'fecha_creacion',
        'fecha_finalizacion',
        'sprint_id'
    ];

    // Relación: Una Historia pertenece a un Sprint
    public function sprint()
    {
        return $this->belongsTo(SprintModel::class, 'sprint_id', 'id');
    }
}