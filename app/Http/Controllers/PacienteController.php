<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    public function index()
    {
        $pacientes = Paciente::all();
        return view('paciente', compact('pacientes'));
    }

    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'nombre_paciente' => 'required|string|max:50',
            'apellido_paciente' => 'required|string|max:50',
            'cedula_paciente' => 'required|string|max:20',
            'sexo_paciente' => 'required|string|in:M,F',
            'edad_paciente' => 'required|integer|min:0',
            'especialidad' => 'required|string|max:50',
            'ubicacion' => 'required|string|max:20',
        ]);

        if ($request->filled('id_paciente')) {
            // Actualizar paciente existente
            $paciente = Paciente::findOrFail($request->id_paciente);
            $paciente->update($validatedData);

        } else {
            // Crear nuevo paciente
            Paciente::create($validatedData);
        }

        return redirect()
            ->route('pacientes.index')
            ->with('success', 'Paciente guardado exitosamente.');
    }
}
