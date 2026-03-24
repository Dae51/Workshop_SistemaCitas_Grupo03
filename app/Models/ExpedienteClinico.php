<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpedienteClinico extends Model
{
    use HasFactory;

    protected $table = 'expedientes_clinicos';

    protected $fillable = [
        'paciente_id',
        'telefono',
        'direccion',
        'fecha_nacimiento',
        'tipo_sangre',
        'alergias',
        'antecedentes',
        'medicamentos_actuales',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
        ];
    }

    public function paciente()
    {
        return $this->belongsTo(User::class, 'paciente_id');
    }
}
