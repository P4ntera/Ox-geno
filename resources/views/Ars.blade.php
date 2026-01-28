@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/ars.css') }}">
@endpush

@section('sidebar-theme', 'sidebar-blue')

@section('content')

    <div class="ars-page">

        {{-- HEADER --}}
        <div class="ars-header">
            <div class="ars-header-left">
                <div class="ars-icon">
                    <i data-lucide="shield"></i>
                </div>

                <div>
                    <h1 class="ars-title">Gestión de Aseguradoras (ARS)</h1>
                    <p class="ars-subtitle">Administrar aseguradoras de salud y precios</p>
                </div>
            </div>

            <button class="btn btn-nueva-ars" data-bs-toggle="modal" data-bs-target="#arsModal" onclick="nuevaARS()">
                <i data-lucide="plus"></i> Nueva Aseguradora
            </button>
        </div>

        {{-- CARD --}}
        <div class="ars-card">

            {{-- TABLA --}}
            <div class="table-wrapper">
                <table class="ars-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre de Aseguradora</th>
                            <th>Pago por Litro</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>

                    <tbody id="tablaARS">

                        @forelse ($arsList as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nombre }}</td>
                                <td>RD$ {{ number_format($item->precio_litro, 1, '.', ',') }}</td>

                                <td class="text-end">

                                    {{-- EDITAR --}}
                                    <button class="btn btn-icon-ghost btn-editar" data-bs-toggle="modal"
                                        data-bs-target="#arsModal" data-id_ars="{{ $item->id_ars }}"
                                        data-nombre="{{ $item->nombre }}" data-precio="{{ $item->precio_litro }}">
                                        <i data-lucide="edit-2"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="sin-registros">No hay aseguradoras registradas</td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>
            </div>

        </div>

    </div>

    {{-- MODAL --}}
    <div class="modal fade" id="arsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">

            <form method="POST" action="{{ route('ars.store') }}" id="formARS">
                @csrf
                <input type="hidden" id="id_ars" name="id_ars">

                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="arsModalLabel">Nueva Aseguradora</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <p class="modal-subtitle">Complete la información de la aseguradora.</p>

                        <div class="row g-3">

                            <div class="col-12">
                                <label class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                onkeypress="soloLetras(event,'errorNombre')" required>
                                <small id="errorNombre" class="text-danger d-none">
                                    Solo se permiten Letras
                                </small>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Precio por Litro *</label>
                                <input type="text" class="form-control" min="1" id="precio" name="precio_litro"
                                    onkeypress="numeroDecimal(event,'errorLitro')" placeholder="0.00"
                                    onpaste="event.preventDefault()" required>
                                <small id="errorLitro" class="text-danger d-none">
                                    Solo se permiten números y 1 punto decimal
                                </small>
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
    <script src="{{ asset('js/validaciones.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const botonesEditar = document.querySelectorAll('.btn-editar');

            botonesEditar.forEach(btn => {
                btn.addEventListener("click", function () {

                    document.getElementById('id_ars').value = this.dataset.id_ars;
                    document.getElementById('nombre').value = this.dataset.nombre;
                    document.getElementById('precio').value = parseFloat(this.dataset.precio).toString();

                    document.getElementById('arsModalLabel').textContent = "Editar Aseguradora";
                });
            });

        });

        // NUEVO
        function nuevaARS() {
            document.getElementById('formARS').reset();
            document.getElementById('id_ars').value = "";
            document.getElementById('arsModalLabel').textContent = "Nueva Aseguradora";
        }

        lucide.createIcons();

    </script>
@endpush