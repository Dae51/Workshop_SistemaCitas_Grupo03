<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicoHorario extends Model
{
    use HasFactory;

    protected $fillable = [
        'medico_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
    ];

    public const DAYS = [
        0 => 'Domingo',
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miercoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sabado',
    ];

    public function medico()
    {
        return $this->belongsTo(User::class, 'medico_id');
    }
}
