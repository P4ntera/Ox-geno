<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConsumoOxigeno;
use App\Models\Paciente;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        
        $pacientes = Paciente::orderBy('nombre_paciente')->get();
        //dd($pacientes->toArray());
        // =========================
        // FILTROS BASE
        // =========================
        // =========================
        $baseQuery = ConsumoOxigeno::with([
            'orden.paciente',
            'habitacion',
            'usuario',
            'estadoDetalle'
        ]);

        // =========================
        // FILTRO POR PACIENTE
        // =========================
        if ($request->filled('paciente_id')) {
            $baseQuery->whereHas('orden', function ($q) use ($request) {
                $q->where('id_paciente', $request->paciente_id);                             
            });
        }

        // =========================
        // FILTRO POR FECHAS
        // =========================
        if ($request->filled('desde') && $request->filled('hasta')) {
            $baseQuery->whereBetween('fecha_inicio', [
                $request->desde . ' 00:00:00',
                $request->hasta . ' 23:59:59'
            ]);
        }

        // =========================
        // 1️⃣ TAB PACIENTE (DETALLE)
        // =========================
        $consumosPaciente = (clone $baseQuery)->get();

        // =========================
        // 2️⃣ TAB PISO (AGRUPADO)
        // =========================
        $consumosPorPiso = ConsumoOxigeno::query()
            ->join('habitaciones', 'habitaciones.id_habitacion', '=', 'consumo_oxigeno.id_habitacion')
            ->join('estado_consumo_detalle', 'estado_consumo_detalle.id_consumo', '=', 'consumo_oxigeno.id_consumo')
            ->selectRaw('
        habitaciones.piso,
        SUM(consumo_oxigeno.volumen_total_litros) as total_litros,
        SUM(estado_consumo_detalle.costo_final) as total_costo
    ')
            ->groupBy('habitaciones.piso')
            ->get();

            //dd($consumosPorPiso->toArray());

        // =========================
        // 3️⃣ TAB FECHA (AGRUPADO)
        // =========================
        $consumosPorFecha = ConsumoOxigeno::query()
            ->join('estado_consumo_detalle', 'estado_consumo_detalle.id_consumo', '=', 'consumo_oxigeno.id_consumo')
            ->selectRaw('
        DATE(consumo_oxigeno.fecha_inicio) as fecha,
        SUM(consumo_oxigeno.volumen_total_litros) as total_litros,
        SUM(estado_consumo_detalle.costo_final) as total_costo
    ')
            ->groupByRaw('DATE(consumo_oxigeno.fecha_inicio)')
            ->orderBy('fecha')
            ->get();

        return view('Reportes', compact(
            'pacientes',
            'consumosPaciente',
            'consumosPorPiso',
            'consumosPorFecha'
        ));
    }

}
