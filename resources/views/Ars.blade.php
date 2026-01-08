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
                            <th>Precio por Litro</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>

                    <tbody id="tablaARS">

                        @forelse ($arsList as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nombre }}</td>
                                <td>RD$ {{ number_format($item->precio_litro) }}</td>

                                <td class="text-end">

                                    {{-- EDITAR --}}
                                    <button class="btn btn-icon-ghost btn-editar" data-bs-toggle="modal"
                                        data-bs-target="#arsModal" data-id_ars="{{ $item->id_ars }}"
                                        data-nombre="{{ $item->nombre }}" data-precio="{{ $item->precio_litro }}">
                                        <i data-lucide="edit-2"></i>
                                    </button>
                                    <button class="btn btn-icon-ghost btn-eliminar" data-id="{{ $item->id_ars }}"
                                        onclick="eliminarARS('{{$item->id_ars}}')">
                                        <i data-lucide="trash-2"></i>
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
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Precio por Litro *</label>
                                <input type="number" step="1" class="form-control" id="precio" name="precio_litro" required>
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

        function eliminarARS(id) {
            Swal.fire({
                title: "¿Eliminar ARS?",
                text: "Esta acción no podrá deshacerse.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/ars/${id}`;

                    form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endpush