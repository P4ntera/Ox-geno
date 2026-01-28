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

        return view('consumo', compact(
            'consumos',
            'pacientes',
            'ordenesActivas',
            'habitaciones'
        ));
    }

    public function store(Request $request)
    {
        //dd($request->all());

        $request->validate([
            'id_habitacion' => 'required',
            'id_orden' => 'required',
            'id_paciente' => 'required',
            'flujo_lpm' => 'required|numeric|min:0.5|max:15'

        ]);

        $consumoExistente = ConsumoOxigeno::where('id_orden', $request->id_orden)
            ->whereNull('fecha_fin')
            ->first();

        if ($consumoExistente) {
            return back()->with('error', 'Este paciente ya tiene un consumo de oxígeno activo. Debe finalizar antes de crear un nuevo consumo.');
        }

        $consumo = ConsumoOxigeno::create([
            'id_orden' => $request->id_orden, // ajusta si usas orden clínica
            'id_habitacion' => $request->id_habitacion,
            'flujo_lpm' => $request->flujo_lpm,
            'fecha_inicio' => now(),
            'volumen_total_litros' => 0,
            'id_usuario' => Auth::id(),
        ]);

        return back()->with('success', 'Consumo iniciado correctamente.');
    }

    public function finalizar($id)
    {
        //dd($id);        

        $consumo = ConsumoOxigeno::findOrFail($id);

        // if ($consumo->fecha_fin) {
        //     return back()->with('error', 'Este consumo ya fue finalizado.');
        // }

        $fin = now();
        $minutos = $consumo->fecha_inicio->diffInMinutes($fin);

        //dd($minutos);

        $fio2 = OrdenOxigeno::where('id_orden', $id)->value('fio2');
        $flujo = $consumo->flujo_lpm;
        //dd($fio2);

        $litros = $minutos * $flujo;
        //dd(round($litros, 2));
        //dd($litros);
        $consumo->update([
            'fecha_fin' => $fin,
            'tiempo_total' => $minutos,
            'volumen_total_litros' => round($litros, 2),
            'estado_clinico' => 0

        ]);

        //dd($consumo->id_orden);

        // OrdenOxigeno::where('id_orden', $consumo->id_orden)
        //     ->update(['estado' => 'Completada']);

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
