@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/centrosalud.css') }}">
@endpush

@section('content')

<div class="centros-page">

    {{-- HEADER --}}
    <div class="centros-header">
        <div class="centros-header-left">
            <div class="centros-icon">
                <i data-lucide="building-2"></i>
            </div>

            <div>
                <h1 class="centros-title">Gestión de Centros de Salud</h1>
                <p class="centros-subtitle">Administrar centros de salud del sistema</p>
            </div>
        </div>

        <button class="btn btn-nuevo-centro"
            data-bs-toggle="modal"
            data-bs-target="#centroModal"
            onclick="nuevoCentro()">
            <i data-lucide="plus"></i> Nuevo Centro
        </button>
    </div>

    {{-- CARD --}}
    <div class="centros-card">

        {{-- BUSCADOR --}}
        <div class="centros-search">
            <i data-lucide="search" class="search-icon"></i>
            <input type="text" id="buscador" class="search-input"
                placeholder="Buscar centro..."
                onkeyup="filtrarCentros()">
        </div>

        {{-- TABLA --}}
        <div class="table-wrapper">
            <table class="centros-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Centro de Salud</th>
                        <th>Dirección</th>
                        <th>Ciudad</th>
                        <th>Pisos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody id="tablaCentros">
                    @forelse($centros as $centro)

                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $centro->nombre_centro }}</td>
                        <td>{{ $centro->direccion_centro }}</td>
                        <td>{{ $centro->ciudad_centro }}</td>
                        <td>{{ $centro->pisos }}</td>

                        <td>

                            {{-- EDITAR --}}
                            <button class="btn btn-icon-ghost btn-editar"
                                data-bs-toggle="modal"
                                data-bs-target="#centroModal"
                                data-id="{{ $centro->id_centro }}"
                                data-nombre="{{ $centro->nombre_centro }}"
                                data-direccion="{{ $centro->direccion_centro }}"
                                data-ciudad="{{ $centro->ciudad_centro }}"
                                data-telefono="{{ $centro->telefono_centro }}"
                                data-pisos="{{ $centro->pisos }}">

                                <i data-lucide="edit"></i>
                            </button>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No hay centros registrados</td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>
</div>

{{-- MODAL --}}
<div class="modal fade" id="centroModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">

        <form method="POST" action="{{ route('centrosalud.store') }}" id="formCentro">
            @csrf
            <input type="hidden" id="centro_id" name="id_centro">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="centroModalLabel">Registrar Nuevo Centro</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <p class="modal-subtitle">Complete la información del centro de salud.</p>

                    <div class="row g-3">

                        <div class="col-md-12">
                            <label class="form-label">Nombre del Centro *</label>
                            <input type="text" class="form-control"
                                   id="nombre_centro" name="nombre_centro" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Dirección *</label>
                            <input type="text" class="form-control"
                                   id="direccion_centro" name="direccion_centro" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Ciudad *</label>
                            <input type="text" class="form-control"
                                   id="ciudad_centro" name="ciudad_centro" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Teléfono *</label>
                            <input type="text" class="form-control"
                                   id="telefono_centro" name="telefono_centro" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Número de Pisos *</label>
                            <input type="number" class="form-control"
                                   id="pisos" name="pisos" min="1" required>
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

            document.getElementById('centro_id').value = this.dataset.id;
            document.getElementById('nombre_centro').value = this.dataset.nombre;
            document.getElementById('direccion_centro').value = this.dataset.direccion;
            document.getElementById('ciudad_centro').value = this.dataset.ciudad;
            document.getElementById('telefono_centro').value = this.dataset.telefono;
            document.getElementById('pisos').value = this.dataset.pisos;
            
            document.getElementById('centroModalLabel').textContent =
                "Editar Centro de Salud";
        });
    });

});

function nuevoCentro() {
    document.getElementById('formCentro').reset();
    document.getElementById('centro_id').value = "";
    document.getElementById('centroModalLabel').textContent =
        "Registrar Nuevo Centro";
}

function filtrarCentros() {
    const filtro = document.getElementById('buscador').value.toLowerCase();
    const filas = document.querySelectorAll('#tablaCentros tr');

    filas.forEach(fila => {
        const texto = fila.textContent.toLowerCase();
        fila.style.display = texto.includes(filtro) ? '' : 'none';
    });
}

lucide.createIcons();
</script>
@endpush
