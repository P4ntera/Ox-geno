@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/usuarios.css') }}">
@endpush

@section('sidebar-theme', 'sidebar-blue')

@section('content')
    <div class="usuarios-page">

        {{-- HEADER --}}
        <div class="usuarios-header">
            <div class="usuarios-header-left">
                <div class="usuarios-icon">
                    <i data-lucide="file-text"></i>
                </div>
                <div>
                    <h1 class="usuarios-title">Reportes de Consumo</h1>
                    <p class="usuarios-subtitle">Análisis del consumo de oxígeno</p>
                </div>
            </div>

            <button class="btn btn-nuevo-usuario" onclick="descargarPDF()">
                <i data-lucide="download"></i>
                <span>Descargar PDF</span>
            </button>
        </div>

        {{-- CARD --}}
        <div class="usuarios-card">

            {{-- FILTROS --}}
            <form id="formFiltros" method="GET" action="{{ route('reportes.index') }}" class="row g-3 mb-4">

                <input type="hidden" name="paciente_nombre" id="paciente_nombre_hidden"
                    value="{{ request('paciente_nombre') }}">

                <input type="hidden" name="paciente_id" id="paciente_id" value="{{ request('paciente_id') }}">
                <input type="hidden" name="piso" id="piso" value="{{ request('piso') }}">

                <div class="col-md-4">
                    <label class="form-label">Paciente</label>

                    <div class="d-flex gap-2">
                        <input type="text" id="paciente_nombre" class="form-control" placeholder="Todos los pacientes"
                            readonly value="{{ request('paciente_nombre') }}">

                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#buscarPacienteModal">
                            Buscar
                        </button>
                    </div>
                </div>


                <div class="col-md-3">
                    <label class="form-label">Desde</label>
                    <input type="date" id="desde" name="desde" class="form-control" value="{{ request('desde') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Hasta</label>
                    <input type="date" id="hasta" name="hasta" class="form-control" value="{{ request('hasta') }}">
                </div>

                <div class="col-md-2 d-flex align-items-end gap-1">
                    <button class="btn btn-success w-100">
                        Filtrar
                    </button>

                    <button type="submit" class="btn btn-primary w-100" onclick="limpiarPaciente()">
                        Limpiar Filtros
                    </button>
                </div>


                <div>

                </div>
            </form>

            {{-- TABS --}}
            <div class="usuarios-search mb-4 d-flex justify-content-center gap-3">
                <button class="btn btn-sm btn-outline-primary me-2 tab active" data-tab="paciente">
                    Por Paciente
                </button>
                <button class="btn btn-sm btn-outline-primary me-2 tab" data-tab="piso">
                    Por Piso
                </button>
                <button class="btn btn-sm btn-outline-primary tab" data-tab="fecha">
                    Por Fecha
                </button>
            </div>

            {{-- ================= TAB PACIENTE ================= --}}
            @if (request('paciente_nombre'))
                <div class="alert alert-info py-2 text-center">
                    Mostrando consumos de:
                    <strong>{{ request('paciente_nombre') }}</strong>
                </div>
            @endif
            <div class="tab-content active" id="paciente">
                <div class="table-wrapper">
                    <table class="usuarios-table">
                        <thead>
                            <tr>
                                <th>Paciente</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Tiempo</th>
                                <th>Flujo</th>
                                <th>Litros</th>
                                <th>Costo Total</th>
                                <th>ARS</th>
                                <th>%</th>
                                <th>Total a pagar</th>
                                <th>Doctor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($consumosPaciente as $c)
                                <tr>
                                    <td>{{ $c->orden->paciente->nombre_paciente }}
                                        {{ $c->orden->paciente->apellido_paciente }}</td>
                                    <td>{{ \Carbon\Carbon::parse($c->fecha_inicio)->format('d/m/Y h:i A') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($c->fecha_fin)->format('d/m/Y h:i A') }}</td>
                                    <td>{{ $c->tiempo_formateado }}</td>
                                    <td>{{ $c->flujo_lpm }} L/m</td>
                                    <td>{{ number_format($c->volumen_total_litros, 2) }} L</td>
                                    <td>RD$ {{ number_format($c->costo_real, 2) }}</td>
                                    <td>RD$ {{ number_format($c->costo_ars, 2) }}</td>
                                    <td>{{ number_format($c->porcentaje_ars, 2) }}%</td>
                                    <td>RD$ {{ number_format($c->costo_final, 2) }}</td>
                                    <td>{{ $c->usuario->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center">No hay consumos registrados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ================= TAB PISO ================= --}}
            <div class="tab-content" id="piso">
                <div class="table-wrapper">
                    <table class="usuarios-table">
                        <thead>
                            <tr>
                                <th>Piso</th>
                                <th>Total Litros</th>
                                <th>Total Facturado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($consumosPorPiso as $p)
                                <tr>
                                    <td>Piso {{ $p->piso }}</td>
                                    <td>{{ number_format($p->total_litros, 2) }} L</td>
                                    <td>RD$ {{ number_format($p->total_costo, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Sin datos por piso</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ================= TAB FECHA ================= --}}
            <div class="tab-content" id="fecha">
                <div class="table-wrapper">
                    <table class="usuarios-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Total Litros</th>
                                <th>Total Facturado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($consumosPorFecha as $f)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($f->fecha)->format('d/m/Y') }}</td>
                                    <td>{{ number_format($f->total_litros, 2) }} L</td>
                                    <td>RD$ {{ number_format($f->total_costo, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Sin datos por fecha</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- ================= MODAL BUSCAR PACIENTE ================= --}}
    <div class="modal fade" id="buscarPacienteModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Buscar Paciente</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="text" class="form-control mb-3" id="buscarPaciente"
                        placeholder="Buscar por nombre o cédula" onkeyup="filtrarPacientes()">

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Paciente</th>
                                <th>Cédula</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tablaPacientes">
                            @foreach ($pacientes as $p)
                                <tr>
                                    <td>{{ $p->nombre_paciente }} {{ $p->apellido_paciente }}</td>
                                    <td>{{ $p->cedula_paciente }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary"
                                            onclick="seleccionarPaciente('{{ $p->id_paciente }}', '{{ $p->nombre_paciente }} {{ $p->apellido_paciente }}')">
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
@endsection

@push('scripts')
    <script>
        // Tabs
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                tab.classList.add('active');
                document.getElementById(tab.dataset.tab).classList.add('active');
            });
        });

        // Validar fechas
        document.getElementById('formFiltros').addEventListener('submit', e => {
            const d = desde.value;
            const h = hasta.value;
            if (d && h && d > h) {
                e.preventDefault();
                alert('La fecha inicial no puede ser mayor a la final');
            }
        });

        function seleccionarPaciente(id, nombre) {
            document.getElementById('paciente_id').value = id;
            document.getElementById('paciente_nombre').value = nombre;
            document.getElementById('paciente_nombre_hidden').value = nombre;

            bootstrap.Modal.getInstance(
                document.getElementById('buscarPacienteModal')
            ).hide();

            document.getElementById('formFiltros').submit();
        }

        function limpiarPaciente() {
            document.getElementById('paciente_id').value = '';
            document.getElementById('paciente_nombre').value = '';
            document.getElementById('paciente_nombre_hidden').value = '';
            document.getElementById('desde').value = '';
            document.getElementById('hasta').value = '';
            document.getElementById('formFiltros').submit();
        }
        lucide.createIcons();

        function filtrarPacientes() {
            const filtro = document.getElementById('buscarPaciente').value.toLowerCase();
            const filas = document.querySelectorAll('#tablaPacientes tr');

            filas.forEach(fila => {
                const texto = fila.textContent.toLowerCase();
                fila.style.display = texto.includes(filtro) ? '' : 'none';
            });
        }
    </script>
@endpush
