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

        $query = ConsumoOxigeno::with(['orden.paciente']);

        if ($request->filled('paciente_id')) {
            $query->where('paciente_id', $request->paciente_id);
        }

        if ($request->filled('piso')) {
            $query->where('piso', $request->piso);
        }

        if ($request->filled('desde') && $request->filled('hasta')) {
            $query->whereBetween('fecha', [$request->desde, $request->hasta]);
        }

        $consumos = $query->get();

        return view('reportes', compact(
            'consumos',
            'pacientes'
        ));
    }
}
