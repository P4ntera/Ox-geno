@extends('layouts.app')

@section('title', 'Ordenes Médicas de Oxígeno')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/orden.css') }}">
@endpush

@section('content')

<div class="orden-page">

    <div class="pacientes-header">
        <div class="pacientes-header-left">
            <div class="pacientes-icon">
                <i data-lucide="clipboard-list"></i>
            </div>

            <div>
                <h1 class="pacientes-title">Gestión de Ordenes Médicas</h1>
                <p class="pacientes-subtitle">Administrar Ordenes Médicas</p>
            </div>
        </div>
    </div>

    {{-- ================= FORMULARIO ================= --}}
    <div class="orden_paciente_card">

        <h5 class="mb-3">Crear Orden Médica</h5>

        <form method="POST" action="{{ route('ordenes.store') }}" id="formOrden">
            @csrf

            <input type="hidden" name="id_paciente" id="id_paciente">

            <div class="row g-3">

                {{-- Paciente --}}
                <div class="col-md-4">
                    <label class="form-label">Paciente *</label>
                    <div class="input-group">
                        <input type="text" id="pacienteNombre" class="form-control" readonly required>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#buscarPacienteModal">
                            Buscar
                        </button>
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">ARS</label>
                    <select name="id_ars" id="id_ars" class="form-select">
                        <option value="">Seleccione</option>
                        @foreach($ars as $a)
                        <option value="{{ $a->id_ars }}">
                            {{ $a->nombre }}
                        </option>
                        @endforeach
                    </select>
                    <small style="color: red;">No es obligatorio</small>
                </div>


                {{-- Relación IE --}}
                <div class="col-md-2">
                    <label class="form-label">Relación I:E *</label>
                    <select name="relacion_ie" class="form-select" required>
                        <option value="1:2">1:2</option>
                        <option value="1:3">1:3</option>
                        <option value="1:4">1:4</option>
                    </select>
                </div>

                {{-- FiO2 --}}
                <div class="col-md-2">
                    <label class="form-label">FiO₂ (%) *</label>
                    <input type="number" name="fio2" class="form-control" min="21" max="100" required>
                </div>

                {{-- Tiempo --}}
                <div class="col-md-2">
                    <label class="form-label">Tiempo (Hora) *</label>
                    <input type="number" name="tiempo" class="form-control" min="1" required>
                </div>

                {{-- Estado --}}
                <div class="col-md-2">
                    <label class="form-label">Estado *</label>
                    <select name="estado" class="form-select">
                        <option value="Activa">Activa</option>
                        <option value="Finalizada">Finalizada</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex justify-content gap-2 align-items-end">
                    <button type="submit" class="btn btn-success">
                        Guardar Orden
                    </button>

                    <button type="button" onclick="document.getElementById('formOrden').reset()"
                        class="btn btn-secondary">
                        Limpiar
                    </button>
                </div>
            </div>
        </form>
    </div>


    {{-- ================= TABLA ================= --}}
    <div class="orden-table-card">

        <div class="search-bar">
            <i data-lucide="search"></i>
            <input type="text" id="buscador" placeholder="Buscar por paciente..." onkeyup="filtrarOrdenes()">
        </div>

        <table class="orden-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Paciente</th>
                    <th>Cédula</th>
                    <th>Médico</th>
                    <th>Ars</th>
                    <th>V3</th>
                    <th>I:E</th>
                    <th>FiO₂</th>
                    <th>Tiempo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody id="tablaOrdenes">
                @forelse($ordenes as $orden)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($orden->created_at)->format('d/m/Y H:i') }}</td>
                    <td>{{ $orden->paciente->nombre_paciente }}</td>
                    <td>{{ $orden->paciente->cedula_paciente }}</td>
                    <td>{{ $orden->usuario->name ?? 'N/A' }}</td>
                    <td>{{ $orden->ars->nombre ?? 'No Proporcionado' }}</td>
                    <td>{{ $orden->v3 }} ml</td>
                    <td>{{ $orden->relacion_ie }}</td>
                    <td>{{ $orden->fio2 }}%</td>
                    <td>{{ $orden->tiempo }} H</td>
                    <td>
                        <span class="estado {{ strtolower($orden->estado) }}">
                            {{ $orden->estado }}
                        </span>
                    </td>
                    <td class="acciones">

                        {{-- EDITAR --}}
                        @if($orden->estado === 'Activa')
                        <button class="btn-icon btn-edit" title="Editar" data-bs-toggle="modal" data-bs-target="#ordenModal"
                            data-id="{{ $orden->id_orden }}" data-relacion="{{ $orden->relacion_ie }}"
                            data-fio2="{{ $orden->fio2 }}" data-tiempo="{{ $orden->tiempo }}"
                            data-ars="{{ $orden->id_ars }}" data-estado="{{ $orden->estado }}">

                            <i data-lucide="edit"></i>
                        </button>
                        @endif


                        {{-- COMPLETAR --}}
                        @if($orden->estado === 'Activa')
                        <button class="btn-icon btn-completar" title="Completar" data-id="{{ $orden->id_orden }}">
                            <i data-lucide="check-circle"></i>
                        </button>
                        @endif

                        {{-- CANCELAR --}}
                        @if($orden->estado === 'Activa')
                        <button class="btn-icon btn-cancelar" title="Cancelar" data-id="{{ $orden->id_orden }}">
                            <i data-lucide="x-circle"></i>
                        </button>
                        @endif

                        {{-- ELIMINAR --}}
                        <button class="btn-icon btn-delete" title="Eliminar" data-id="{{ $orden->id_orden }}">
                            <i data-lucide="trash"></i>
                        </button>

                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">No hay órdenes médicas registradas</td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>

{{-- ================= MODAL EDITAR ORDEN ================= --}}

<div class="modal fade" id="ordenModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Editar Orden Médica</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('ordenes.store') }}" id="formEditarOrden">
                @csrf
                <input type="hidden" name="id_orden" id="edit_id_orden">

                <div class="modal-body row g-3">

                    <div class="col-md-4">
                        <label class="form-label">Relación I:E</label>
                        <select name="relacion_ie" id="edit_relacion" class="form-select">
                            <option value="1:2">1:2</option>
                            <option value="1:3">1:3</option>
                            <option value="1:4">1:4</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">FiO₂ (%)</label>
                        <input type="number" name="fio2" id="edit_fio2" class="form-control" min="21" max="100">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tiempo (Hora)</label>
                        <input type="number" name="tiempo" id="edit_tiempo" class="form-control" min="1">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">ARS</label>
                        <select name="id_ars" id="edit_ars" class="form-select">
                            <option value="">Seleccione</option>
                            @foreach($ars as $a)
                            <option value="{{ $a->id_ars }}">{{ $a->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary">Guardar cambios</button>
                </div>

            </form>

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

                <input type="text" id="filtroPacientes" class="form-control mb-3"
                    placeholder="Buscar por nombre o cédula">

                <table class="table table-bordered table-pacientes">
                    <thead>
                        <tr>
                            <th class="text-center">Nombre</th>
                            <th class="text-center">Apellido</th>
                            <th class="text-center">Cédula</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="listaPacientes">
                        @foreach($pacientes as $p)
                        <tr class="fila-paciente">
                            <td class="text-center">{{ $p->nombre_paciente }}</td>
                            <td class="text-center">{{ $p->apellido_paciente }}</td>
                            <td class="text-center">{{ $p->cedula_paciente }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-success btn-sm seleccionar-paciente"
                                    data-id="{{ $p->id_paciente }}" data-nombre="{{ $p->nombre_paciente }} {{ $p->apellido_paciente }}">
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

<script src="{{ asset('/js/alertas.js') }}"></script>

<script>
    // Validar que se seleccione un paciente antes de enviar el formulario

    document.getElementById('formOrden').addEventListener('submit', function(e) {

        const idPaciente = document.getElementById('id_paciente').value;

        if (!idPaciente) {
            e.preventDefault();

            alertaPacienteRequerido();
        }
    });

    // Eventos de acciones 

    document.addEventListener('DOMContentLoaded', () => {

        // COMPLETAR
        document.querySelectorAll('.btn-completar').forEach(btn => {
            btn.addEventListener('click', () => {
                confirmarCompletar(btn.dataset.id);
            });
        });

        // CANCELAR
        document.querySelectorAll('.btn-cancelar').forEach(btn => {
            btn.addEventListener('click', () => {
                confirmarCancelar(btn.dataset.id);
            });
        });

        // ELIMINAR
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', () => {
                confirmarEliminar(btn.dataset.id);
            });
        });

        // EDITAR
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                editarOrden(btn.dataset.id);
            });
        });

    });

    lucide.createIcons();

    // Nueva orden: limpiar formulario

    function nuevaOrden() {
        document.getElementById('formOrden').reset();
        document.getElementById('id_orden').value = '';
    }

    // Filtrar órdenes en la tabla

    function filtrarOrdenes() {
        const filtro = document.getElementById('buscador').value.toLowerCase();
        document.querySelectorAll('#tablaOrdenes tr').forEach(tr => {
            tr.style.display = tr.textContent.toLowerCase().includes(filtro) ? '' : 'none';
        });
    }

    // Seleccionar paciente del modal
    document.querySelectorAll('.seleccionar-paciente').forEach(btn => {
        btn.addEventListener('click', function() {

            const pacienteId = this.dataset.id;
            const pacienteNombre = this.dataset.nombre;

            // Pasar datos al formulario
            document.getElementById('id_paciente').value = pacienteId;
            document.getElementById('pacienteNombre').value = pacienteNombre;

            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(
                document.getElementById('buscarPacienteModal')
            );
            modal.hide();
        });
    });

    // Filtrar pacientes en el modal

    document.getElementById('filtroPacientes').addEventListener('keyup', function() {
        const filtro = this.value.toLowerCase();
        document.querySelectorAll('#listaPacientes tr').forEach(tr => {
            tr.style.display = tr.textContent.toLowerCase().includes(filtro) ? '' : 'none';
        });
    });

    // Rellenar datos en el modal de edición

    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {

            document.getElementById('edit_id_orden').value = this.dataset.id;
            document.getElementById('edit_relacion').value = this.dataset.relacion;
            document.getElementById('edit_fio2').value = this.dataset.fio2;
            document.getElementById('edit_tiempo').value = this.dataset.tiempo;
            document.getElementById('edit_ars').value = this.dataset.ars;

        });
    });
</script>
@endpush