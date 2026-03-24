<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Illuminate\Http\Request;

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
            'estado' => 'required|string|max:50',
        ]);

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
            'estado' => 'sometimes|required|string|max:50',
        ]);

        $cita->update($validated);

        return response()->json($cita);
    }

    public function destroy(string $id)
    {
        $cita = Cita::findOrFail($id);
        $cita->delete();

        return response()->json(null, 204);
    }
}
