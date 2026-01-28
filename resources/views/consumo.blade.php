@extends('layouts.app')

@section('title', 'Sistema de Gestión de Oxígeno')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/consumo.css') }}">
@endpush

@section('content')

    <div class="orden-page">
        {{-- ================= HEADER ================= --}}
        <div class="pacientes-header">
            <div class="pacientes-header-left">
                <div class="pacientes-icon">
                    <i data-lucide="wind"></i>
                </div>
                <div>
                    <h1 class="pacientes-title">Registro de Consumo de Oxígeno</h1>
                    <p class="pacientes-subtitle">Registrar y controlar el consumo por paciente</p>
                </div>
            </div>
        </div>

        {{-- ================= FORMULARIO ================= --}}

        @if (!$ordenesActivas->count())
            <div class="orden-form-card" style="border-left:6px solid #f59e0b;background:#fffbeb">

                <h5 style="color:#b45309">
                    <i data-lucide="alert-triangle"></i>
                    No hay órdenes médicas activas
                </h5>

                <p style="color:#92400e;margin:0">
                    Debe crear o activar una orden médica antes de registrar consumo de oxígeno.
                </p>
            </div>
        @else
            <div class="orden_paciente_card">
                <h5 class="mb-3">Registrar Nuevo Consumo</h5>

                <form method="POST" action="{{ route('consumo.store') }}" id="formConsumo">
                    @csrf
                    <input type="hidden" name="id_orden" id="id_orden">
                    <input type="hidden" name="id_paciente" id="id_paciente">
                    <input type="hidden" name="id_habitacion" id="id_habitacion">

                    <div class="row g-3">

                        {{-- Orden Médica --}}
                        <div class="col-md-4">
                            <label class="form-label">Orden Médica *</label>
                            <div class="input-group">
                                <input type="text" id="ordenPaciente" class="form-control" readonly required>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#buscarOrdenModal">
                                    Buscar
                                </button>
                            </div>
                        </div>

                        {{-- Fecha --}}
                        <div class="col-md-3">
                            <label class="form-label">Fecha</label>
                            <input type="text" class="form-control" value="{{ now()->format('d/m/Y') }}" readonly>
                        </div>

                        {{-- Hora --}}
                        <div class="col-md-3">
                            <label class="form-label">Hora</label>
                            <input type="text" class="form-control" value="{{ now()->format('h:i A') }}" readonly>
                        </div>

                        {{-- Habitación --}}
                        <div class="col-md-4">
                            <label class="form-label">Habitación *</label>
                            <div class="input-group">
                                <input type="text" id="habitacionNombre" class="form-control" readonly required>

                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#buscarHabitacionModal">
                                    Buscar
                                </button>
                            </div>
                        </div>

                        {{-- Flujo O2 --}}
                        <div class="col-md-2">
                            <label class="form-label">Flujo de Oxigeno *</label>
                            <input type="number" min="0.5" step="0.5" class="form-control" id="flujo_lpm"
                                name="flujo_lpm" min="0" onkeypress="numeroDecimal(event,'errorflujo')" required>
                            <small id="errorflujo" class="text-danger d-none">
                                Solo se permiten números y un punto decimal.
                            </small>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-success">
                            <i data-lucide="play"></i> Iniciar Consumo
                        </button>

                        <button type="button" class="btn btn-secondary" onclick="limpiarConsumo()">
                            Limpiar
                        </button>
                    </div>
                </form>
            </div>
        @endif
        {{-- ================= TABLA ================= --}}
        <div class="orden-table-card">
            <div class="search-bar">
                <i data-lucide="search"></i>
                <input type="text" id="buscador" placeholder="Buscar por paciente..." onkeyup="filtrarTabla()">
            </div>

            <table class="orden-table" id="tablaConsumo">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Paciente</th>
                        <th>Cédula</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Final</th>
                        <th>Tiempo Transcurrido</th>
                        <th>Flujo</th>
                        <th>Relación IE</th>
                        <th>Fio2</th>
                        <th>Litros Consumidos o2</th>
                        <th>Ubicación</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($consumos as $c)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $c->orden->paciente->nombre_paciente }} {{ $c->orden->paciente->apellido_paciente }}</td>
                            <td>{{ $c->orden->paciente->cedula_paciente }}</td>
                            <td>{{ \Carbon\Carbon::parse($c->fecha_inicio)->format('d/m/Y h:i A') }}</td>
                            <td>
                                @if ($c->fecha_fin)
                                    {{ \Carbon\Carbon::parse($c->fecha_fin)->format('d/m/Y h:i A') }}
                                @else
                                    ---
                                @endif
                            </td>
                            <td>
                                @if ($c->estado_clinico)
                                    <span class="badge badge-success timer" data-inicio="{{ $c->fecha_inicio }}">
                                        <i data-lucide="clock"></i>
                                        <span class="time">00:00:00</span>
                                    </span>
                                @else
                                    @php
                                        $min = $c->tiempo_total;

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
                                    @endphp

                                    {{ $texto }}
                                @endif
                            </td>
                            <td>{{ number_format($c->flujo_lpm, 2, '.') }}L/m</td>
                            <td>{{ $c->orden->relacion_ie }}</td>
                            <td>{{ $c->orden->fio2 }}%</td>
                            <td>{{ number_format($c->volumen_total_litros, 2, '.', ',') }} L</td>
                            <td>
                                Piso {{ $c->habitacion->piso }},
                                Hab. {{ $c->habitacion->numero_habitacion }}
                            </td>

                            <td>
                                <span class="badge-status {{ $c->estado_clinico ? 'activo' : 'finalizado' }}">
                                    {{ $c->estado_clinico ? 'Activo' : 'Finalizado' }}
                                </span>
                            </td>
                            <td class="acciones">

                                @if ($c->estado_clinico)
                                    <form method="POST" action="{{ route('consumo.finalizar', $c->id_consumo) }}"
                                        class="d-inline">
                                        @csrf
                                        <button class="btn btn-danger btn-sm">
                                            Finalizar
                                        </button>
                                    </form>
                                @endif

                                {{-- EDITAR --}}
                                @if ($c->estado_clinico)
                                    <button class="btn btn-icon-ghost btn-editar" data-bs-toggle="modal"
                                        data-bs-target="#editarConsumoModal" data-id="{{ $c->id }}"
                                        data-paciente="{{ $c->orden->paciente->nombre_paciente }}"
                                        data-flujo="{{ $c->flujo }}" data-piso="{{ $c->piso }}"
                                        data-habitacion="{{ $c->habitacion }}">
                                        <i data-lucide="edit"></i>
                                    </button>
                                @endif
                                {{-- FIN EDITAR --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center">No hay registros</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= MODAL BUSCAR ORDEN ================= --}}
    <div class="modal fade" id="buscarOrdenModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Buscar Orden Médica Activa</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="text" id="filtroOrdenes" class="form-control mb-3"
                        placeholder="Buscar por paciente u orden">

                    <table class="table table-bordered table-pacientes">
                        <thead>
                            <tr>
                                <th>Paciente</th>
                                <th>Fecha de Creación</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="listaOrdenes">
                            @forelse ($ordenesActivas as $o)
                                <tr>
                                    <td>{{ $o->paciente->nombre_paciente }} {{ $o->paciente->apellido_paciente }}</td>
                                    <td>{{ $o->created_at }}</td>
                                    <td>
                                        <button type="button" class="btn btn-success btn-sm seleccionar-orden"
                                            data-id="{{ $o->id_orden }}"
                                            data-paciente="{{ $o->paciente->nombre_paciente }} {{ $o->paciente->apellido_paciente }}"
                                            data-paciente-id="{{ $o->paciente->id_paciente }}">
                                            Seleccionar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No hay registros</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>

            </div>
        </div>
    </div>
    {{-- ================= FIN MODAL BUSCAR ORDEN ================= --}}

    {{-- ================= MODAL BUSCAR HABITACIÓN ================= --}}
    <div class="modal fade" id="buscarHabitacionModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Buscar Habitación</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="text" id="filtroHabitaciones" class="form-control mb-3"
                        placeholder="Buscar por número o piso">

                    <table class="table table-bordered table-pacientes">
                        <thead>
                            <tr>
                                <th>Piso</th>
                                <th>Habitación</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="listaHabitaciones">
                            @forelse($habitaciones as $h)
                                <tr>
                                    <td class="text-center">{{ $h->piso }}</td>
                                    <td class="text-center">{{ $h->numero_habitacion }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-success seleccionar-habitacion"
                                            data-id="{{ $h->id_habitacion }}"
                                            data-nombre="Piso {{ $h->piso }} - Habitación {{ $h->numero_habitacion }} ">
                                            Seleccionar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No hay registros</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>

            </div>
        </div>
    </div>
    {{-- ================= FIN MODAL BUSCAR HABITACIÓN ================= --}}

    {{-- ================= MODAL EDITAR CONSUMO ================= --}}
    <div class="modal fade" id="editarConsumoModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">

            <form method="POST" action="{{ route('consumo.store') }}" id="formEditarConsumo">
                @csrf
                <input type="hidden" name="id" id="edit_id">

                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Editar Consumo</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Paciente</label>
                                <input type="text" id="edit_paciente" class="form-control" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Flujo O₂ (L/min)</label>
                                <input type="number" name="flujo" id="edit_flujo" class="form-control"
                                    step="0.5" min="0.5" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Piso</label>
                                <input type="number" name="piso" id="edit_piso" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Habitación</label>
                                <input type="text" name="habitacion" id="edit_habitacion" class="form-control"
                                    required>
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button class="btn btn-primary">Guardar Cambios</button>
                    </div>

                </div>
            </form>

        </div>
    </div>
@endsection
{{-- ================= FIN MODAL EDITAR CONSUMO ================= --}}

@push('scripts')
    <script src="{{ asset('/js/alertas.js') }}"></script>
    <script src="{{ asset('/js/validaciones.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('success'))
        <script>
            alertSuccess(@json(session('success')));
        </script>
    @endif

    @if (session('error'))
        <script>
            alertError(@json(session('error')));
        </script>
    @endif

    @if (session('warning'))
        <script>
            alertWarning(@json(session('warning')));
        </script>
    @endif

    <script>
        lucide.createIcons();

        //ALERTAS DE PACIENTES Y ORDENES REQUERIDAS
        document.getElementById('formConsumo').addEventListener('submit', function(e) {

            const idOrden = document.getElementById('id_orden').value;
            const idPaciente = document.getElementById('id_paciente').value;
            const id_habitacion = document.getElementById('id_habitacion').value;

            if (!idOrden) {
                e.preventDefault();

                alertaOrdenRequerido();
            } else if (!id_habitacion) {
                e.preventDefault();

                alertaHabitacionRequerida();
            }
        });

        //FIN ALERTAS DE PACIENTES Y ORDENES REQUERIDAS

        //SELECCIONAR ORDEN Y HABITACION
        document.addEventListener('DOMContentLoaded', () => {

            // Seleccionar orden médica
            document.querySelectorAll('.seleccionar-orden').forEach(btn => {
                btn.addEventListener('click', function() {

                    document.getElementById('id_orden').value = this.dataset.id;
                    document.getElementById('id_paciente').value = this.dataset.pacienteId;
                    document.getElementById('ordenPaciente').value = this.dataset.paciente;

                    bootstrap.Modal.getInstance(
                        document.getElementById('buscarOrdenModal')
                    ).hide();
                });
            });

            // FILTRAR ORDENES
            document.getElementById('filtroOrdenes')?.addEventListener('keyup', function() {
                const filtro = this.value.toLowerCase();
                document.querySelectorAll('#listaOrdenes tr').forEach(tr => {
                    tr.style.display = tr.innerText.toLowerCase().includes(filtro) ? '' : 'none';
                });
            });

            // SELECCIONAR HABITACION
            document.querySelectorAll('.seleccionar-habitacion').forEach(btn => {
                btn.addEventListener('click', function() {

                    document.getElementById('id_habitacion').value = this.dataset.id;
                    document.getElementById('habitacionNombre').value = this.dataset.nombre;

                    bootstrap.Modal.getInstance(
                        document.getElementById('buscarHabitacionModal')
                    ).hide();
                });
            });

            // Filtro de habitaciones
            document.getElementById('filtroHabitaciones').addEventListener('keyup', function() {
                const filtro = this.value.toLowerCase();

                document.querySelectorAll('#listaHabitaciones tr').forEach(row => {
                    row.style.display = row.textContent.toLowerCase().includes(filtro) ?
                        '' :
                        'none';
                });
            });

            //FIN DE SELECCIONAR ORDEN Y HABITACION

            // Editar consumo
            document.querySelectorAll('.btn-editar').forEach(btn => {
                btn.addEventListener('click', function() {

                    document.getElementById('edit_id').value = this.dataset.id;
                    document.getElementById('edit_paciente').value = this.dataset.paciente;
                    document.getElementById('edit_flujo').value = this.dataset.flujo;
                    document.getElementById('edit_habitacion').value = this.dataset.habitacion;
                });
            });

        });

        function limpiarConsumo() {
            document.getElementById('formConsumo').reset();
            document.getElementById('id_orden').value = '';
            document.getElementById('pacienteNombre').value = '';

        }

        //FILTRO DE TABLA DE CONSUMOS
        function filtrarTabla() {
            const filtro = document.getElementById('buscador').value.toLowerCase();
            document.querySelectorAll('#tablaConsumo tbody tr').forEach(tr => {
                tr.style.display = tr.innerText.toLowerCase().includes(filtro) ? '' : 'none';
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.timer').forEach(timer => {
                const inicio = new Date(timer.dataset.inicio.replace(' ', 'T'));
                const timeSpan = timer.querySelector('.time');

                function actualizar() {
                    const ahora = new Date();
                    let diff = Math.floor((ahora - inicio) / 1000);

                    const dias = Math.floor(diff / 86400);
                    diff %= 86400;

                    const horas = Math.floor(diff / 3600);
                    diff %= 3600;

                    const minutos = Math.floor(diff / 60);
                    const segundos = diff % 60;

                    let texto = '';

                    if (dias > 0) {
                        texto += `${dias} día${dias > 1 ? 's' : ''}`;
                    }

                    if (horas > 0) {
                        texto += (texto ? ', ' : '') + `${horas} h`;
                    }

                    if (minutos > 0) {
                        texto += (texto ? ' ' : '') + `${minutos} min`;
                    }

                    // Si aún no llega a 1 minuto
                    if (!texto) {
                        texto = 'Recién iniciado';
                    }

                    timeSpan.textContent = texto;
                }
                actualizar();
                setInterval(actualizar, 1000);
            });
        });
    </script>
@endpush
