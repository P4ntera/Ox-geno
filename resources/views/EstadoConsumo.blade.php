    @extends('layouts.app')

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/usuarios.css') }}">
    @endpush

    @section('content')
        <div class="usuarios-page">

            {{-- HEADER --}}
            <div class="usuarios-header">
                <div class="usuarios-header-left">
                    <div class="usuarios-icon">
                        <i data-lucide="file-text"></i>
                    </div>
                    <div>
                        <h1 class="usuarios-title">Estado de Consumo</h1>
                        <p class="usuarios-subtitle">
                            Resumen estimado de consumo por paciente
                        </p>
                    </div>
                </div>
            </div>

            {{-- CARD --}}
            <div class="usuarios-card">

                {{-- BLOQUE PACIENTE --}}
                <form method="POST" action="{{ route('estado_consumo.seleccionar') }}" id="estado_consumo">
                    @csrf

                    <input type="hidden" name="id_paciente" id="id_paciente">

                    <div class="mb-4 p-3 rounded bg-light border">

                        <label class="form-label">Paciente *</label>

                        <div class="input-group">
                            <input type="text" id="pacienteNombre" class="form-control"
                                placeholder="Seleccione un paciente"
                                value="{{ $pacienteSeleccionado
                                    ? $pacienteSeleccionado->nombre_paciente .
                                        ' ' .
                                        $pacienteSeleccionado->apellido_paciente .
                                        ' ' .
                                        $pacienteSeleccionado->cedula_paciente
                                    : '' }}"
                                readonly>

                            <button type="button" class="btn btn-primary px-4" data-bs-toggle="modal"
                                data-bs-target="#buscarPacienteModal">
                                Buscar
                            </button>

                            {{-- botón oculto para submit --}}
                            <button type="submit" id="btnBuscar" hidden></button>

                        </div>

                    </div>
                </form>

                {{-- TABLA --}}
                <div class="table-wrapper">
                    <table class="usuarios-table">
                        <thead>
                            <tr>
                                <th>Paciente</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Tiempo Transcurrido</th>
                                <th>Flujo</th>
                                <th>Litros Consumidos</th>
                                <th>Costo Total</th>
                                <th>Seguro Cubre</th>
                                <th>Porcentaje Cubierto</th>
                                <th>Total a pagar</th>
                                <th>Doctor</th>
                            </tr>
                        </thead>
                        <tbody id="tablaConsumos">
                            @forelse ($consumos as $consumo)
                                <tr>
                                    <td>{{ $consumo->orden->paciente->nombre_paciente }}
                                        {{ $consumo->orden->paciente->apellido_paciente }} </td>
                                    <td>{{ \Carbon\Carbon::parse($consumo->fecha_inicio)->format('d/m/Y h:i A') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($consumo->fecha_fin)->format('d/m/Y h:i A') }}</td>
                                    <td>
                                        {{ $consumo->tiempo_formateado }}
                                    </td>
                                    <td>{{ $consumo->flujo_lpm }}L/m</td>
                                    <td>{{ number_format($consumo->volumen_total_litros, 2, '.', ',') }} L</td>
                                    <td>RD$ {{ number_format($consumo->costo_real, 2, '.', ',') }}</td>
                                    <td>RD$ {{ number_format($consumo->costo_ars, 2, '.', ',') }}</td>
                                    <td>{{ number_format($consumo->porcentaje_ars, 2, '.', ',') }} %</td>
                                    <td>RD$ {{ number_format($consumo->costo_final, 2, '.', ',') }}</td>
                                    {{-- <td>
                                        @if ($consumo->estimado_texto)
                                            <span class="badge bg-success">
                                                {{ $consumo->estimado_texto }}
                                            </span>
                                        @else
                                            RD$ {{ number_format($consumo->estimado, 2, '.', ',') }}
                                        @endif
                                    </td> --}}

                                    <td>{{ $consumo->usuario->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No hay consumos para este paciente.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- TOTALES --}}
                <div class="mt-3">
                    <<strong>Total litros:</strong>
                        {{ number_format($totales['litros'], 2) }} L<br>

                        <strong>Total tiempo:</strong>
                        {{ $totales['tiempo_formateado'] }}<br>

                        <strong>Costo real total:</strong>
                        RD$ {{ number_format($totales['costo_real'], 2) }}<br>

                        <strong>Seguro cubre:</strong>
                        RD$ {{ number_format($totales['costo_ars'], 2) }}<br>

                        <strong>Total a pagar:</strong>
                        <strong>RD$ {{ number_format($totales['costo_final'], 2) }}</strong>

                </div>

                {{-- BOTÓN --}}
                <div class="text-end mt-4">
                    <button class="btn btn-danger" id="btnCerrar" {{ $pacienteSeleccionado ? '' : 'disabled' }}>
                        <i data-lucide="file-text"></i>
                        Generar PDF / Estado de Consumo
                    </button>

                    <form id="formCerrarConsumo" method="POST" action="{{ route('estado_consumo.cerrar') }}">
                        @csrf
                        <input type="hidden" name="id_paciente" value="{{ $pacienteSeleccionado->id_paciente ?? '' }}">
                    </form>

                </div>

            </div>
        </div>

        {{-- MODAL BUSCAR PACIENTE --}}
        <div class="modal fade" id="buscarPacienteModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Buscar Paciente</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <input type="text" id="buscadorPaciente" class="form-control mb-3"
                            placeholder="Buscar por nombre o cédula..." onkeyup="filtrarPacientes()">

                        <table class="usuarios-table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Cédula</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody id="tablaPacientes">
                                @foreach ($pacientes as $p)
                                    <tr>
                                        <td>{{ $p->nombre_paciente }} {{ $p->apellido_paciente }}</td>
                                        <td>{{ $p->cedula_paciente }}</td>
                                        <td>
                                            <button type="button" class="btn btn-success btn-sm seleccionar-paciente"
                                                data-id="{{ $p->id_paciente }}"
                                                data-nombre="{{ $p->nombre_paciente }} {{ $p->apellido_paciente }} {{ $p->cedula_paciente }}">
                                                Seleccionar
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
        {{-- Fin MODAL --}}
    @endsection
    @push('scripts')
        <script src="{{ asset('/js/alertas.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                document.querySelectorAll('.seleccionar-paciente').forEach(btn => {
                    btn.addEventListener('click', function() {

                        const pacienteId = this.dataset.id;
                        const pacienteNombre = this.dataset.nombre;

                        document.getElementById('id_paciente').value = pacienteId;
                        document.getElementById('pacienteNombre').value = pacienteNombre;

                        const modal = bootstrap.Modal.getInstance(
                            document.getElementById('buscarPacienteModal')
                        );
                        modal.hide();

                        document.getElementById('estado_consumo').submit();
                    });
                });

            });

            document.getElementById('btnCerrar').addEventListener('click', function() {
                cerrarConsumo();
            });
        </script>
    @endpush
