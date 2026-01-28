<?php

namespace App\Http\Controllers;

use App\Models\CentroSalud;
use App\Models\HealthCenter;
use Illuminate\Http\Request;

class CentroSaludController extends Controller
{
    public function index()
    {
        $centros = CentroSalud::all();
        return view('centrosalud', compact('centros'));
    }

    public function store(Request $request)
    {
        //dd($request->all());

        $validatedData = $request->validate([
            'nombre_centro' => 'required|string|max:255',
            'direccion_centro' => 'required|string',
            'ciudad_centro' => 'required|string|max:100',
            'telefono_centro' => 'required|string|max:20',
            'pisos' => 'required|integer|min:1',
            'habitaciones' => 'nullable|integer|min:0', // si lo usas
            'pago_litro' => 'required|numeric|min:0',
        ]);

        // SI LLEGA id_centro → ACTUALIZAR
        if ($request->filled('id_centro')) {

            $centro = CentroSalud::findOrFail($request->id_centro);
            $centro->update($validatedData);

        } else {
            // CREAR NUEVO
            CentroSalud::create($validatedData);
        }

        return redirect()
            ->route('centrosalud.index')
            ->with('success', 'Centro de salud guardado correctamente.');
    }

}
