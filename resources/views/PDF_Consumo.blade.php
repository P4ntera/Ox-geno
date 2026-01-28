<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Estado de Consumo</title>
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 14px;
        }

        h2 {
            text-align: center;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 6px;
            text-align: center;
        }

        th {
            background: #6286e7;
        }

        .right {
            text-align: right;
        }

        .badge {
            padding: 4px 6px;
            border-radius: 4px;
            font-size: 10px;
        }

        .success {
            background: #28a745;
            color: #fff;
        }
    </style>
</head>

<body>

    <h2>Estado de Consumo de Oxígeno</h2>

    <table width="100%" style="margin-bottom: 20px;">
        <tr>
            <td style="width: 60%; vertical-align: top; border: none; text-align: left;">
                <p style="margin: 4px 0; text-align: left;">
                    <strong>Paciente:</strong>
                    {{ $estado->paciente->nombre_paciente }}
                    {{ $estado->paciente->apellido_paciente }}
                </p>

                <p style="margin: 4px 0; text-align: left;">
                    <strong>Identificación:</strong>
                    {{ $estado->paciente->cedula_paciente }}
                </p>

                <p style="margin: 4px 0; text-align: left;">
                    <strong>Fecha de cierre:</strong>
                    {{ \Carbon\Carbon::parse($estado->fecha_cierre)->format('d/m/Y h:i A') }}
                </p>

                <p style="margin: 4px 0; text-align: left;">
                    <strong>Generado por:</strong>
                    {{ $estado->usuario->name }}
                </p>
            </td>


            <td style="width: 40%; vertical-align: top; text-align: right; border: none;">
                <p style="margin: 4px 0; text-align: right;">
                    <strong>Litros Consumidos:</strong>
                    {{ number_format($estado->total_litros, 2) }} L
                </p>

                <p style="margin: 4px 0; text-align: right;">
                    <strong>Total Estadía:</strong>
                    {{ $estado->total_tiempo }} min
                </p>

                <p style="margin: 4px 0; text-align: right;">
                    <strong>Costo Total:</strong>
                    RD$ {{ 0 }}
                </p>

                <p style="margin: 4px 0; text-align: right;">
                    <strong>Total a pagar:</strong>
                    RD$ {{ number_format($estado->total_costo, 2) }}
                </p>
            </td>
        </tr>
    </table>

    <hr>



    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Tiempo</th>
                <th>Flujo</th>
                <th>Fio2</th>
                <th>Relación I:E</th>
                <th>Litros</th>
                <th>Total a pagar</th>
                <th>Doctor</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($estado->detalles as $detalle)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($detalle->consumo->fecha_inicio)->format('d/m/Y h:i A') }}</td>
                    <td>{{ \Carbon\Carbon::parse($detalle->consumo->fecha_fin)->format('d/m/Y h:i A') }}</td>
                    <td>{{ $detalle->consumo->tiempo_total }} min</td>
                    <td>{{ $detalle->consumo->flujo_lpm }} L/m</td>
                    <td>{{ $detalle->consumo->orden->fio2 }} %</td>
                    <td>{{ $detalle->consumo->orden->relacion_ie }}</td>
                    <td class="right">
                        {{ number_format($detalle->consumo->volumen_total_litros, 2) }} L
                    </td>
                    <td class="right">
                        @if ($detalle->cubierto_ars)
                            <span class="badge success">Cubierto por ARS</span>
                        @else
                            RD$ {{ number_format($detalle->costo_real, 2) }}
                        @endif
                    </td>
                    <td>{{ $detalle->consumo->orden->usuario->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <hr>
</body>

</html>
