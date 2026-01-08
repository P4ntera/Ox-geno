<?php

namespace App\Http\Controllers;

use App\Models\Habitacion;
use App\Models\CentroSalud;
use Illuminate\Http\Request;

class HabitacionController extends Controller
{
    public function index()
    {
        $habitaciones = Habitacion::with('centro')->get();
        $centros = CentroSalud::all();

        return view('habitacion', compact('habitaciones', 'centros'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_centro' => 'required|integer',
            'piso' => 'required|integer|min:1',
            'numero_habitacion' => 'required|integer|min:1',
        ]);

        // EDITAR
        if ($request->id_habitacion) {
            $habitacion = Habitacion::findOrFail($request->id_habitacion);
            $habitacion->update($validated);

            return back()->with('success', 'Habitación actualizada correctamente.');
        }

        // CREAR
        Habitacion::create($validated);

        return back()->with('success', 'Habitación creada correctamente.');
    }

    public function destroy($id)
    {
        Habitacion::findOrFail($id)->delete();

        return back()->with('success', 'Habitación eliminada correctamente.');
    }
}
