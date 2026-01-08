<?php

namespace App\Http\Controllers;

use App\Models\ConsumoOxigeno;
use App\Models\Habitacion;
use App\Models\OrdenOxigeno;
use App\Models\Paciente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsumoOxigenoController extends Controller
{   
    public function index()
    {
        $consumos = ConsumoOxigeno::with(['orden.paciente', 'habitacion'])
            ->get();

        $pacientes = Paciente::all();
        $habitaciones = Habitacion::all();

        $ordenesActivas = OrdenOxigeno::with('paciente')
            ->where('estado', 'Activa')
            ->get();

        //dd($consumos);

        return view('consumo', compact(
            'consumos',
            'pacientes',
            'ordenesActivas',
            'habitaciones'
        ));
    }

    public function store(Request $request)
    {

        dd($request->all());

        $v3 = 

        $request->validate([
            'flujo' => 'required|numeric|min:0.5',
            'piso' => 'required',
            'habitacion' => 'required',
        ]);

        

        $consumo = ConsumoOxigeno::create([
            'id_orden' => $request->paciente_id, // ajusta si usas orden clínica
            'id_habitacion' => $request->habitacion,
            'fecha_inicio' => now(),
            'volumen_total_litros' => 0,
            'costo_total' => 0,
            'id_usuario' => Auth::id(),
        ]);

        return back()->with('success', 'Consumo iniciado correctamente.');
    }

    public function finalizar($id)
    {
        $consumo = ConsumoOxigeno::findOrFail($id);

        if ($consumo->fecha_fin) {
            return back()->with('error', 'Este consumo ya fue finalizado.');
        }

        $fin = now();
        $minutos = $consumo->fecha_inicio->diffInMinutes($fin);

        // ⚠️ Ajusta el flujo real si lo manejas en otra tabla
        $flujo = 5;
        $litros = $minutos * $flujo;

        $consumo->update([
            'fecha_fin' => $fin,
            'volumen_total_litros' => round($litros, 2),
            'costo_total' => 0, // aquí luego aplicas ARS
        ]);

        return back()->with('success', 'Consumo finalizado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $consumo = ConsumoOxigeno::findOrFail($id);

        $request->validate([
            'id_habitacion' => 'required',
        ]);

        $consumo->update($request->only([
            'id_habitacion',
            'fecha_inicio',
            'fecha_fin',
            'volumen_total_litros',
            'costo_total',
        ]));

        return back()->with('success', 'Registro actualizado.');
    }

    public function destroy($id)
    {
        ConsumoOxigeno::findOrFail($id)->delete();

        return back()->with('success', 'Registro eliminado.');
    }
}
