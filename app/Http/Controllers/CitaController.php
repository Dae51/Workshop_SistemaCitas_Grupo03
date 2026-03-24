<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\User;
use App\Support\AppointmentSchedule;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CitaController extends Controller
{
    public function index()
    {
        return response()->json(Cita::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'paciente_id' => 'required|exists:users,id',
            'medico_id' => 'required|exists:users,id',
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'estado' => 'required|in:pendiente,confirmada,cancelada',
        ]);

        $validated = $this->validateAppointment($validated);
        $cita = Cita::create($validated);

        return response()->json($cita, 201);
    }

    public function show(string $id)
    {
        $cita = Cita::findOrFail($id);
        return response()->json($cita);
    }

    public function update(Request $request, string $id)
    {
        $cita = Cita::findOrFail($id);

        $validated = $request->validate([
            'paciente_id' => 'sometimes|required|exists:users,id',
            'medico_id' => 'sometimes|required|exists:users,id',
            'fecha' => 'sometimes|required|date',
            'hora' => 'sometimes|required|date_format:H:i',
            'estado' => 'sometimes|required|in:pendiente,confirmada,cancelada',
        ]);

        $validated = $this->validateAppointment([
            'paciente_id' => $validated['paciente_id'] ?? $cita->paciente_id,
            'medico_id' => $validated['medico_id'] ?? $cita->medico_id,
            'fecha' => $validated['fecha'] ?? $cita->fecha->format('Y-m-d'),
            'hora' => $validated['hora'] ?? substr($cita->hora, 0, 5),
            'estado' => $validated['estado'] ?? $cita->estado,
        ], $cita);

        $cita->update($validated);

        return response()->json($cita);
    }

    public function destroy(string $id)
    {
        $cita = Cita::findOrFail($id);
        $cita->delete();

        return response()->json(null, 204);
    }

    protected function validateAppointment(array $data, ?Cita $cita = null): array
    {
        $doctor = User::query()
            ->whereKey($data['medico_id'])
            ->where('role', User::ROLE_MEDICO)
            ->first();

        if (! $doctor) {
            throw ValidationException::withMessages([
                'medico_id' => 'Debes seleccionar un medico valido.',
            ]);
        }

        if (! AppointmentSchedule::doctorWorksAt($doctor, $data['fecha'], $data['hora'])) {
            throw ValidationException::withMessages([
                'hora' => 'El medico no atiende en ese horario.',
            ]);
        }

        if (AppointmentSchedule::hasConflict($doctor, $data['fecha'], $data['hora'], $cita?->id)) {
            throw ValidationException::withMessages([
                'hora' => 'Ya existe una cita para ese medico en ese bloque de tiempo.',
            ]);
        }

        $data['hora'] = AppointmentSchedule::normalizeTime($data['hora']);

        return $data;
    }
}
