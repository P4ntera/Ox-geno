@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pacientes.css') }}">
@endpush

@section('content')

<div class="pacientes-page">

    <div class="pacientes-header">
        <div class="pacientes-header-left">
            <div class="pacientes-icon">
                <i data-lucide="user"></i>
            </div>

            <div>
                <h1 class="pacientes-title">Gestión de Pacientes</h1>
                <p class="pacientes-subtitle">Administrar pacientes registrados</p>
            </div>
        </div>

        <button class="btn btn-nuevo-paciente" data-bs-toggle="modal" data-bs-target="#pacienteModal"
            onclick="nuevoPaciente()">
            <i data-lucide="user-plus"></i> Nuevo Paciente
        </button>
    </div>

    <div class="pacientes-card">

        {{-- BUSCADOR --}}
        <div class="pacientes-search">
            <i data-lucide="search" class="search-icon"></i>
            <input type="text" id="buscador" class="search-input" placeholder="Buscar paciente por nombre o cédula"
                onkeyup="filtrarPacientes()">
        </div>

        {{-- TABLA --}}
        <div class="table-wrapper">
            <table class="pacientes-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Cédula</th>
                        <th>Sexo</th>
                        <th>Edad</th>
                        <th>Especialidad</th>
                        <th>Ubicación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody id="tablaPacientes">
                    @forelse($pacientes as $paciente)

                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $paciente->nombre_paciente }} </td>
                        <td>{{ $paciente->apellido_paciente }}</td>
                        <td>{{ $paciente->cedula_paciente }}</td>
                        <td>{{ $paciente->sexo_paciente }}</td>
                        <td>{{ $paciente->edad_paciente }} años</td>
                        <td>{{ $paciente->especialidad }}</td>
                        <td>{{ $paciente->ubicacion }}</td>
                        <td>

                            {{-- EDITAR --}}
                            <button class="btn btn-icon-ghost btn-editar" data-bs-toggle="modal"
                                data-bs-target="#pacienteModal" data-id="{{ $paciente->id_paciente }}"
                                data-nombre="{{ $paciente->nombre_paciente }}"
                                data-apellido="{{ $paciente->apellido_paciente }}"
                                data-cedula="{{ $paciente->cedula_paciente }}"
                                data-sexo="{{ $paciente->sexo_paciente }}"
                                data-edad="{{ $paciente->edad_paciente }}"
                                data-especialidad="{{ $paciente->especialidad }}"
                                data-ubicacion="{{ $paciente->ubicacion }}">
                                <i data-lucide="edit"></i>
                            </button>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No hay pacientes registrados</td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>
</div>

{{-- MODAL --}}
<div class="modal fade" id="pacienteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">

        <form method="POST" action="{{ route('pacientes.store') }}" id="formPaciente">
            @csrf
            <input type="hidden" id="paciente_id" name="id_paciente">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="pacienteModalLabel">Registrar Nuevo Paciente</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <p class="modal-subtitle">Complete la información del paciente.</p>

                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre_paciente" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Apellido *</label>
                            <input type="text" class="form-control" id="apellido" name="apellido_paciente" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Cédula *</label>
                            <input type="text" class="form-control" id="cedula" name="cedula_paciente" maxlength="13" pattern="\d{3}-\d{7}-\d" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Sexo *</label>
                            <select name="sexo_paciente" id="sexo" class="form-select" required>
                                <option value="">Seleccione</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Edad *</label>
                            <input type="number" class="form-control" id="edad" name="edad_paciente" min="0" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Especialidad *</label>
                            <input type="text" id="especialidad" name="especialidad" class="form-control" required>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label">Ubicación *</label>
                            <input type="text" id="ubicacion" name="ubicacion" class="form-control" required>
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
    // FUNCION EDITAR PACIENTE
    document.addEventListener("DOMContentLoaded", function() {

        const editar = document.querySelectorAll('.btn-editar');

        editar.forEach(btn => {
            btn.addEventListener("click", function() {

                document.getElementById('paciente_id').value = this.dataset.id;
                document.getElementById('nombre').value = this.dataset.nombre;
                document.getElementById('apellido').value = this.dataset.apellido;
                document.getElementById('cedula').value = this.dataset.cedula;
                document.getElementById('sexo').value = this.dataset.sexo;
                document.getElementById('edad').value = this.dataset.edad;
                document.getElementById('especialidad').value = this.dataset.especialidad;
                document.getElementById('ubicacion').value = this.dataset.ubicacion;
                document.getElementById('pacienteModalLabel').textContent = "Editar Paciente";
            });
        });
    });
    // FINAL FUNCION EDITAR PACIENTE

    // FUNCION LIMPIAR FORMULARIO PARA NUEVO PACIENTE

    function nuevoPaciente() {

        document.getElementById('formPaciente').reset();
        document.getElementById('paciente_id').value = "";
        document.getElementById('pacienteModalLabel').textContent = "Registrar Nuevo Paciente";

    }

    // FINAL FUNCION LIMPIAR FORMULARIO PARA NUEVO PACIENTE

    // FUNCION BUSCADOR PARA PACIENTES 

    function filtrarPacientes() {
        const filtro = document.getElementById('buscador').value.toLowerCase();
        const filas = document.querySelectorAll('#tablaPacientes tr');

        let visibles = 0;

        filas.forEach((fila, index) => {

            // Evitar filas vacías o mensajes
            if (!fila.children.length) return;

            const nombre = fila.children[1].textContent.toLowerCase();
            const apellido = fila.children[2].textContent.toLowerCase();
            const cedula = fila.children[3].textContent.toLowerCase();

            const coincide =
                nombre.includes(filtro) ||
                apellido.includes(filtro) ||
                cedula.includes(filtro);

            if (coincide && visibles < 5) {
                fila.style.display = '';
                visibles++;
            } else {
                fila.style.display = 'none';
            }
        });
    }

    // FINAL FUNCION BUSCADOR PARA PACIENTES

    // VALIDACION DEL FORMATO DE CEDULA
    document.getElementById('cedula').addEventListener('input', function(e) {
        let valor = e.target.value.replace(/\D/g, ''); // solo números

        if (valor.length > 11) valor = valor.slice(0, 11);

        if (valor.length > 3 && valor.length <= 10) {
            valor = valor.slice(0, 3) + '-' + valor.slice(3);
        } else if (valor.length > 10) {
            valor = valor.slice(0, 3) + '-' + valor.slice(3, 10) + '-' + valor.slice(10);
        }

        e.target.value = valor;
    });

    // FINAL VALIDACION DEL FORMATO DE CEDULA

    lucide.createIcons();
</script>
@endpush