<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_MEDICO = 'medico';
    public const ROLE_ASISTENTE = 'asistente';
    public const ROLE_PACIENTE = 'paciente';

    public const ROLES = [
        self::ROLE_ADMIN => 'Administrador',
        self::ROLE_MEDICO => 'Medico',
        self::ROLE_ASISTENTE => 'Asistente',
        self::ROLE_PACIENTE => 'Paciente',
    ];

    // Estos son los únicos campos que dejo meter asíncronamente en bloque
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'activo',
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
            'activo' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->activo
            && in_array($this->role, [
                self::ROLE_ADMIN,
                self::ROLE_MEDICO,
                self::ROLE_ASISTENTE,
            ], true);
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

    public function expedienteClinico()
    {
        return $this->hasOne(ExpedienteClinico::class, 'paciente_id');
    }

    public function horarios()
    {
        return $this->hasMany(MedicoHorario::class, 'medico_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isMedico(): bool
    {
        return $this->role === self::ROLE_MEDICO;
    }

    public function isAsistente(): bool
    {
        return $this->role === self::ROLE_ASISTENTE;
    }

    public function isPaciente(): bool
    {
        return $this->role === self::ROLE_PACIENTE;
    }
}
