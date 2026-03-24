<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    use HasFactory;
    
    // Acá le forcé el nombre a mano porque a veces Laravel trata de pluralizar en inglés
    protected $table = 'especialidades';

    protected $fillable = ['nombre'];

    // Esta relación es para traer fácil todos los doctores que tengan asignada esta especialidad
    public function medicos()
    {
        return $this->hasMany(User::class, 'especialidad_id')->where('role', 'medico');
    }
}
