<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Ars;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PacienteController extends Controller
{
    public function index()
    {
        $pacientes = Paciente::all();
        $ars = Ars::all();
        return view('paciente', compact('pacientes', 'ars'));
    }

    public function store(Request $request)
    {

        //dd($request->all());

        $validatedData = $request->validate([
            
            'nombre_paciente' => 'required|string|max:50',
            'apellido_paciente' => 'required|string|max:50',
            'cedula_paciente' => [
                'required',
                'string',
                'max:20',
                Rule::unique('paciente', 'cedula_paciente')
                    ->ignore(   $request->id_paciente, 'id_paciente') // 🔥 ignora si es edición
            ],
            'id_ars' => 'nullable|exists:ars,id_ars',
            'sexo_paciente' => 'required|string|in:M,F',
            'edad_paciente' => 'required|integer|min:0',
            'especialidad' => 'required|string|max:50',
            'ubicacion' => 'required|string|max:20',

            [
                // Nombre
                'nombre_paciente.required' => 'El nombre del paciente es obligatorio.',
                'nombre_paciente.max' => 'El nombre no puede superar los 50 caracteres.',

                // Apellido
                'apellido_paciente.required' => 'El apellido del paciente es obligatorio.',
                'apellido_paciente.max' => 'El apellido no puede superar los 50 caracteres.',

                // Cédula
                'cedula_paciente.required' => 'La cédula es obligatoria.',
                'cedula_paciente.unique' => 'Esta cédula ya está registrada.',
                'cedula_paciente.max' => 'La cédula no puede superar los 20 caracteres.',

                // ARS
                'id_ars.exists' => 'El ARS seleccionado no es válido.',

                // Sexo
                'sexo_paciente.required' => 'Debe seleccionar el sexo.',
                'sexo_paciente.in' => 'El sexo debe ser M o F.',

                // Edad
                'edad_paciente.required' => 'La edad es obligatoria.',
                'edad_paciente.integer' => 'La edad debe ser un número.',
                'edad_paciente.min' => 'La edad no puede ser negativa.',

                // Especialidad
                'especialidad.required' => 'La especialidad es obligatoria.',

                // Ubicación
                'ubicacion.required' => 'La ubicación es obligatoria.',
            ]
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
