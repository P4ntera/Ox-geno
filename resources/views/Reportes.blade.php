@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/reportes.css') }}">
@endpush

@section('content')

<div class="reportes-page">

    {{-- HEADER --}}
    <div class="reportes-header">
        <div class="reportes-header-left">
            <div class="reportes-icon">
                <i data-lucide="file-text"></i>
            </div>
            <div>
                <h1 class="reportes-title">Reportes de Consumo</h1>
                <p class="reportes-subtitle">
                    Análisis y reportes detallados del consumo de oxígeno
                </p>
            </div>
        </div>
    </div>

    {{-- CARD PRINCIPAL --}}
    <div class="reportes-card">

        {{-- FILTROS --}}
        <div class="reportes-filtros">

            <div class="filtro">
                <label>Paciente</label>
                <button class="btn btn-outline-secondary w-100"
                    data-bs-toggle="modal"
                    data-bs-target="#buscarPacienteModal">
                    Buscar paciente
                </button>
            </div>

            <div class="filtro">
                <label>Fecha Desde</label>
                <input type="date" name="desde">
            </div>

            <div class="filtro">
                <label>Fecha Hasta</label>
                <input type="date" name="hasta">
            </div>

            <div class="filtro filtro-btn">
                <button class="btn btn-primary">
                    <i data-lucide="filter"></i> Filtrar
                </button>
            </div>

        </div>

        {{-- KPIs --}}
        <div class="reportes-kpis">
            <div class="kpi-card">
                <span>Total Consumo</span>
                <h3>1803.00 L</h3>
                <small>15 registros</small>
            </div>

            <div class="kpi-card">
                <span>Costo Total</span>
                <h3>$79,823.88</h3>
                <small>Según ARS</small>
            </div>

            <div class="kpi-card">
                <span>Promedio por Registro</span>
                <h3>120.20 L</h3>
                <small>Por aplicación</small>
            </div>
        </div>

        {{-- TABS --}}
        <div class="reportes-tabs">
            <button class="tab active" data-tab="paciente">Por Paciente</button>
            <button class="tab" data-tab="piso">Por Piso</button>
            <button class="tab" data-tab="fecha">Por Fecha</button>
        </div>

        {{-- CONTENIDO --}}
        <div class="tab-content active" id="paciente">
            <table class="tabla-reporte">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Piso</th>
                        <th>Fecha</th>
                        <th>Litros</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($consumos as $c)
                    <tr>
                        <td>{{ $c->orden->paciente->nombre_paciente }}</td>
                        <td>{{ $c->habitacion->piso }}</td>
                        <td>{{ $c->fecha }}</td>
                        <td>{{ $c->litros }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">No hay datos</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


    </div>
</div>

<!-- Modal Buscar Paciente -->

<div class="modal fade" id="buscarPacienteModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Buscar Paciente</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="text" class="form-control mb-3"
                    placeholder="Buscar por nombre o cédula">

                <table class="table">
                    <thead>
                        <tr>
                            <th>Paciente</th>
                            <th>Cédula</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Pedro Santana</td>
                            <td>001-1234567-8</td>
                            <td>
                                <button class="btn btn-sm btn-primary">
                                    Seleccionar
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<!--Fin Modal Buscar Paciente -->

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

    lucide.createIcons();
</script>
@endpush