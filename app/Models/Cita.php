<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $fillable = [
        'paciente_id',
        'medico_id',
        'fecha',
        'hora',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    // Relación para sacar fácilmente todos los datos del paciente con solo tener la cita
    public function paciente()
    {
        return $this->belongsTo(User::class, 'paciente_id');
    }

    // Al revés, relación para jalar al doctor y su data desde de la cita
    public function medico()
    {
        return $this->belongsTo(User::class, 'medico_id');
    }
}
