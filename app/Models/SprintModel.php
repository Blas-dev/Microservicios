<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SprintModel extends Model
{
    protected $table = 'sprints';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_fin'
    ];
}