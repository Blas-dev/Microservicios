<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SprintModel extends Model
{
    // Indicamos el nombre exacto de la tabla en la base de datos
    protected $table = 'sprints';

    // Clave primaria
    protected $primaryKey = 'id';

    // Estos son los campos que permitimos guardar o actualizar masivamente
    protected $fillable = [
        'nombre', 
        'fecha_inicio', 
        'fecha_fin'
    ];

    // Relación: Un Sprint tiene muchas Historias
    public function historias()
    {
        return $this->hasMany(HistoriasModel::class, 'sprint_id', 'id');
    }
}