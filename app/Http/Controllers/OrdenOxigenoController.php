<?php

namespace App\Http\Controllers;

use App\Models\OrdenOxigeno;
use App\Models\Paciente;
use App\Models\Ars;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Termwind\Components\Dd;

class OrdenOxigenoController extends Controller
{
    public function index()
    {
        $ordenes = OrdenOxigeno::with(['paciente', 'usuario'])
            ->orderByDesc('created_at')
            ->get();

        $pacientes = Paciente::all();

        return view('OrdenOxigeno', compact('ordenes', 'pacientes'));
    }

    public function store(Request $request)
    {
        //dd(auth::id());
        //dd($request->all());

        $request->validate([
            'id_paciente' => 'required|exists:paciente,id_paciente',
            'relacion_ie' => 'required|in:1:2,1:3,1:4',
            'fio2' => 'required|integer|min:21|max:100',
        ]);

        $ordenActiva = OrdenOxigeno::where('id_paciente', $request->id_paciente)
            ->where('estado', 'Activa')
            ->first();

        if ($ordenActiva) {
            return back()->with('error', 'Este paciente ya tiene una orden de oxígeno activa. Debe completarla antes de crear una nueva.');
        }

        $paciente = Paciente::find($request->id_paciente);
        $v3 = $paciente->sexo_paciente === 'M' ? 500 : 450;

        OrdenOxigeno::Create(
            [
                'id_paciente' => $request->id_paciente,
                'id_usuario' => Auth::id(),
                'v3' => $v3,
                'relacion_ie' => $request->relacion_ie,
                'fio2' => $request->fio2,
                'estado' => 'Activa',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        return back()->with('success', 'Orden médica guardada correctamente.');
    }

    public function completar($id)
    {
        //dd($id);

        $orden = OrdenOxigeno::findOrFail($id);

        $orden->update(['estado' => 'Completada']);

        return back()->with('success', 'Orden completada.');
    }

    public function cancelar($id)
    {
        $orden = OrdenOxigeno::findOrFail($id);

        $orden->update(['estado' => 'Cancelada']);

        return back()->with('success', 'Orden cancelada.');
    }

}
