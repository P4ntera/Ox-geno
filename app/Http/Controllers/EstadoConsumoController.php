<?php

namespace App\Http\Controllers;

use App\Models\CentroSalud;
use App\Models\OrdenOxigeno;
use Illuminate\Http\Request;
use App\Models\ConsumoOxigeno;
use App\Models\EstadoConsumo;
use App\Models\EstadoConsumoDetalle;
use App\Models\Paciente;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DB;
use function PHPUnit\Framework\isEmpty;

class EstadoConsumoController extends Controller
{
    public function index(Request $request)
    {

        $estado = EstadoConsumo::where('id_estado_consumo', operator: 3)->first();
        $resumen = $estado->load([
            'paciente',
            'detalles.consumo',
            'usuario'
        ]);

        return Pdf::loadView('PDF_Consumo', [
            'estado' => $estado

        ])
            ->setPaper('A4', 'landscape')
            ->stream(
                'estado_consumo_' . $estado->id_estado_consumo . '.pdf'
            );

        $pacienteId = session('estado_consumo_paciente_id');

        $consumos = collect();

        $totales = [
            'litros' => 0,
            'tiempo' => 0,
            'costo' => 0,
            'tiempo_formateado' => '',
        ];

        $pacienteSeleccionado = null;

        if ($pacienteId) {

            $pacienteSeleccionado = Paciente::find($pacienteId);

            $consumos = ConsumoOxigeno::whereIn('id_orden', function ($q) use ($request, $pacienteId) {
                $q->select('id_orden')
                    ->from('orden_oxigeno')
                    ->where('id_paciente', $pacienteId);
            })
                ->whereNotIn('id_consumo', function ($q) {
                    $q->select('id_consumo')
                        ->from('estado_consumo_detalle');
                })
                ->get();

            //dd($consumos->toArray());

            $totales['litros'] = $consumos->sum('volumen_total_litros');
            $totales['tiempo'] = $consumos->sum('tiempo_total');
            $totales['tiempo_formateado'] = $this->calculartiempo($totales['tiempo']);

            $totales['costo'] = 0;

            foreach ($consumos as $consumo) {
                $consumo->estimado = $this->calcularEstimado($consumo);

                if ($consumo->estimado < 0) {
                    $consumo->estimado = 0;
                    $consumo->estimado_texto = 'Totalmente cubierto por ARS';

                } else {
                    $consumo->estimado_texto = null;
                    $totales['costo'] += $consumo->estimado;
                }
                $consumo->tiempo_formateado = $this->calculartiempo($consumo->tiempo_total);
            }
        }

        $pacientes = Paciente::all();

        return view('EstadoConsumo', compact(
            'consumos',
            'pacientes',
            'totales',
            'pacienteSeleccionado'
        ));
    }

    function calculartiempo($min)
    {
        $dias = intdiv($min, 1440);
        $min %= 1440;

        $horas = intdiv($min, 60);
        $min %= 60;

        $texto = '';

        if ($dias > 0) {
            $texto .= $dias . ' día' . ($dias > 1 ? 's' : '');
        }
        if ($horas > 0) {
            $texto .= ($texto ? ', ' : '') . $horas . ' h';
        }
        if ($min > 0) {
            $texto .= ($texto ? ' ' : '') . $min . ' min';
        }

        if (!$texto) {
            $texto = '0 min';
        }

        return $texto;
    }
    public function seleccionarPaciente(Request $request)
    {
        //dd('Seleccionar paciente - funcionalidad en desarrollo', $request->all());
        $request->validate([
            'id_paciente' => 'required|exists:paciente,id_paciente'
        ]);

        session(['estado_consumo_paciente_id' => $request->id_paciente]);

        return redirect()->route('estado_consumo.index');
    }

    function calcularEstimado(ConsumoOxigeno $consumo)
    {
        $orden = $consumo->orden;

        $v3 = $orden->v3; // 500 / 450
        $rie = $this->mapRelacionIE($orden->relacion_ie);
        $fio2 = $orden->fio2;

        $base = $v3 + $rie + $fio2;

        $tiempo = $consumo->tiempo_total;

        $seguro_paciente = optional($orden->paciente->ars)->precio_litro ?? 0;
        //dd($seguro_paciente);
        $centro_salud = CentroSalud::where('id_centro', 1)->value('pago_litro');

        $costo_litro = $seguro_paciente > 0
            ? $centro_salud - $seguro_paciente
            : $centro_salud;

        //dd($v3, $rie, $fio2, $tiempo, $seguro_paciente, $centro_salud, $costo_litro);

        $rd = $base + ($tiempo * $costo_litro);

        //dd($v3, $rie, $fio2, $tiempo, $seguro_paciente, $centro_salud, $costo_litro, $rd);

        return round($rd, 2);
    }

    function mapRelacionIE(string $relacion): float
    {
        $map = [
            '1:1' => 1.0,
            '1:2' => 2.0,
            '1:3' => 3.0,
            '1:4' => 4.0,
        ];

        return $map[$relacion] ?? 1.0; // fallback seguro
    }

    public function cerrar(Request $request)
    {
        $estado = null;

        DB::transaction(function () use ($request, &$estado) {

            $consumos = ConsumoOxigeno::whereIn('id_orden', function ($q) use ($request) {
                $q->select('id_orden')
                    ->from('orden_oxigeno')
                    ->where('id_paciente', $request->id_paciente);
            })
                ->whereNotIn('id_consumo', function ($q) {
                    $q->select('id_consumo')->from('estado_consumo_detalle');
                })
                ->get();

            if ($consumos->isEmpty()) {
                abort(400, 'No hay consumos para cerrar.');
            }

            $totalCosto = 0;

            $precioArs = optional(
                $consumos->first()->orden->paciente->ars
            )->precio_litro ?? 0;

            $precioCentro = CentroSalud::where('id_centro', 1)->value('pago_litro');

            $estado = EstadoConsumo::create([
                'id_paciente' => $request->id_paciente,
                'fecha_cierre' => now(),
                'total_litros' => $consumos->sum('volumen_total_litros'),
                'total_tiempo' => $consumos->sum('tiempo_total'),
                'total_costo' => 0,
                'precio_base_ars' => $precioArs,
                'precio_base_centro' => $precioCentro,
                'id_usuario' => auth()->id(),
            ]);

            foreach ($consumos as $consumo) {

                $estimado = $this->calcularEstimado($consumo);

                $cubiertoArs = 0;

                if ($estimado <= 0) {
                    $estimado = 0;
                    $cubiertoArs = 1;
                } else {
                    $totalCosto += $estimado;
                }

                EstadoConsumoDetalle::create([
                    'id_estado_consumo' => $estado->id_estado_consumo,
                    'id_consumo' => $consumo->id_consumo,
                    'costo' => $estimado,
                    'cubierto_ars' => $cubiertoArs,
                ]);
            }

            $estado->update([
                'total_costo' => $totalCosto
            ]);
        });

        session()->forget('estado_consumo_paciente_id');

        return redirect()
            ->route('estado_consumo.resumen', $estado->id_estado_consumo)
            ->with('success', 'Estado de consumo cerrado exitosamente.');
    }

    function resumen(EstadoConsumo $estado)
    {
        $estado->load([
            'paciente',
            'detalles.consumo',
            'usuario'
        ]);

        return Pdf::loadView('PDF_Consumo', [
            'estado' => $estado
        ])
        ->setPaper('A4', 'landscape')
        ->stream(
                'estado_consumo_' . $estado->id_estado_consumo . '.pdf'
            );
    }
}


