<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use Illuminate\Http\Request;

class EspecialidadController extends Controller
{
    public function index()
    {
        return response()->json(Especialidad::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string'
        ]);

        $especialidad = Especialidad::create($validated);

        return response()->json($especialidad, 201);
    }

    public function show(string $id)
    {
        $especialidad = Especialidad::findOrFail($id);
        return response()->json($especialidad);
    }

    public function update(Request $request, string $id)
    {
        $especialidad = Especialidad::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'descripcion' => 'nullable|string'
        ]);

        $especialidad->update($validated);

        return response()->json($especialidad);
    }

    public function destroy(string $id)
    {
        $especialidad = Especialidad::findOrFail($id);
        $especialidad->delete();

        return response()->json(null, 204);
    }
}
