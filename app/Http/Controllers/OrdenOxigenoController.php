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
        $ordenes = OrdenOxigeno::with(['paciente', 'usuario', 'ars'])
            ->orderByDesc('created_at')
            ->get();

        $pacientes = Paciente::all();
        $ars = Ars::all();

        return view('OrdenOxigeno', compact('ordenes', 'pacientes', 'ars'));
    }

    public function store(Request $request)
    {
        //dd(auth::id());
        //dd($request->all());

        $validator = $request->validate([
            'relacion_ie' => 'required|in:1:2,1:3,1:4',
            'fio2' => 'required|integer|min:21|max:100',
            'tiempo' => 'required|integer|min:1',
            'id_ars' => 'nullable|exists:ars,id_ars',
        ]);

        if ($request->id_orden) {
            $orden = OrdenOxigeno::findOrFail($request->id_orden);

            $orden->update(
                [
                    'relacion_ie' => $request->relacion_ie,
                    'fio2' => $request->fio2,
                    'tiempo' => $request->tiempo,
                    'id_ars' => $request->id_ars,
                    'updated_at' => now(),
                ]
            );

        } else {

            $paciente = Paciente::find($request->id_paciente);
            $v3 = $paciente->sexo_paciente === 'M' ? 500 : 450;


            OrdenOxigeno::Create(
                [
                    'id_paciente' => $request->id_paciente,
                    'id_usuario' => Auth::id(),
                    'v3' => $v3,
                    'relacion_ie' => $request->relacion_ie,
                    'fio2' => $request->fio2,
                    'tiempo' => $request->tiempo,
                    'id_ars' => $request->id_ars,
                    'estado' => 'Activa',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

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
