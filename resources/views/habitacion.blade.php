@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/habitacion.css') }}">
@endpush

@section('content')

<div class="habitaciones-page">

    {{-- HEADER --}}
    <div class="habitaciones-header">
        <div class="habitaciones-header-left">
            <div class="habitaciones-icon">
                <i data-lucide="bed"></i>
            </div>

            <div>
                <h1 class="habitaciones-title">Gestión de Habitaciones</h1>
                <p class="habitaciones-subtitle">Administrar habitaciones por centro de salud</p>
            </div>
        </div>

        <button class="btn btn-nueva-habitacion"
            data-bs-toggle="modal"
            data-bs-target="#habitacionModal"
            onclick="nuevaHabitacion()">
            <i data-lucide="plus"></i> Nueva Habitación
        </button>
    </div>

    {{-- CARD --}}
    <div class="habitaciones-card">

        {{-- BUSCADOR --}}
        <div class="habitaciones-search">
            <i data-lucide="search" class="search-icon"></i>
            <input type="text" id="buscador" class="search-input"
                placeholder="Buscar habitación..." onkeyup="filtrarHabitaciones()">
        </div>

        {{-- TABLA --}}
        <div class="table-wrapper">
            <table class="habitaciones-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Centro</th>
                        <th>Piso</th>
                        <th>Número</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody id="tablaHabitaciones">
                    @forelse($habitaciones as $hab)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $hab->centro->nombre_centro }}</td>
                        <td>{{ $hab->piso }}</td>
                        <td>{{ $hab->numero_habitacion }}</td>

                        <td class="text-begin">
                            {{-- Editar --}}
                            <button class="btn btn-icon-ghost btn-editar"
                                data-bs-toggle="modal"
                                data-bs-target="#habitacionModal"
                                data-id="{{ $hab->id_habitacion }}"
                                data-centro="{{ $hab->id_centro }}"
                                data-piso="{{ $hab->piso }}"
                                data-numero="{{ $hab->numero_habitacion }}">
                                <i data-lucide="edit"></i>
                            </button>

                            {{-- Eliminar --}}
                            <form action="{{ route('habitaciones.destroy', $hab->id_habitacion) }}"
                                method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-icon-ghost text-danger"
                                        onclick="return confirm('¿Eliminar habitación?')">
                                    <i data-lucide="trash-2"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="5" class="sin-registros">No hay habitaciones registradas</td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>
</div>

{{-- MODAL --}}
<div class="modal fade" id="habitacionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">

        <form method="POST" action="{{ route('habitaciones.store') }}" id="formHabitacion">
            @csrf
            <input type="hidden" id="id_habitacion" name="id_habitacion">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="habitacionModalLabel">Registrar Nueva Habitación</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <p class="modal-subtitle">Complete los datos de la habitación.</p>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Centro *</label>
                            <select class="form-select" id="id_centro" name="id_centro" required>
                                <option value="">Seleccione...</option>
                                @foreach($centros as $c)
                                    <option value="{{ $c->id_centro }}">{{ $c->nombre_centro }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Piso *</label>
                            <input type="number" class="form-control" id="piso" name="piso" min="1" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Número *</label>
                            <input type="number" class="form-control" id="numero_habitacion" name="numero_habitacion" required>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary">Guardar</button>
                </div>

            </div>

        </form>

    </div>
</div>

@endsection


@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const editar = document.querySelectorAll('.btn-editar');

    editar.forEach(btn => {
        btn.addEventListener("click", function () {
            document.getElementById('id_habitacion').value = this.dataset.id;
            document.getElementById('id_centro').value = this.dataset.centro;
            document.getElementById('piso').value = this.dataset.piso;
            document.getElementById('numero_habitacion').value = this.dataset.numero;
            document.getElementById('habitacionModalLabel').textContent = "Editar Habitación";
        });
    });
});

function nuevaHabitacion() {
    document.getElementById('formHabitacion').reset();
    document.getElementById('id_habitacion').value = "";
    document.getElementById('habitacionModalLabel').textContent = "Registrar Nueva Habitación";
}

function filtrarHabitaciones() {
    const filtro = document.getElementById('buscador').value.toLowerCase();
    const filas = document.querySelectorAll('#tablaHabitaciones tr');

    filas.forEach(fila => {
        const texto = fila.textContent.toLowerCase();
        fila.style.display = texto.includes(filtro) ? '' : 'none';
    });
}

lucide.createIcons();
</script>
@endpush
