<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Estos son los únicos campos que dejo meter asíncronamente en bloque
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'especialidad_id',
    ];

    // Oculto la pass en los arrays para cuando retornemos el usuario en JSON por la API
    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
    
    // Relación de Eloquent: Un médico pertenece a una especialidad
    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class);
    }
    
    // Relación de Eloquent: Traigo todas las citas donde este usuario es el paciente usando el paciente_id
    public function citasComoPaciente()
    {
        return $this->hasMany(Cita::class, 'paciente_id');
    }
    
    // Relación de Eloquent: Traigo todas las citas donde este usuario es el doctor que atiende (usando medico_id)
    public function citasComoMedico()
    {
        return $this->hasMany(Cita::class, 'medico_id');
    }
}
