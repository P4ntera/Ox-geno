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
          <i data-lucide="users"></i>
        </div>
        <div>
          <h1 class="usuarios-title">Gestión de Usuarios</h1>
          <p class="usuarios-subtitle">Administrar usuarios del sistema</p>
        </div>
      </div>

      <button class="btn btn-nuevo-usuario" data-bs-toggle="modal" data-bs-target="#usuarioModal"
        onclick="nuevoUsuario()">
        <i data-lucide="user-plus"></i>
        <span>Nuevo Usuario</span>
      </button>
    </div>

    {{-- CARD PRINCIPAL --}}
    <div class="usuarios-card">

      {{-- BUSCADOR --}}
      <div class="usuarios-search">
        <i data-lucide="search" class="search-icon"></i>
        <input type="text" id="buscador" class="search-input" placeholder="Buscar por nombre, usuario o rol..."
          onkeyup="filtrarUsuarios()">
      </div>

      {{-- TABLA --}}
      <div class="table-wrapper">
        <table class="usuarios-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Nombre Completo</th>
              <th>Usuario</th>
              <th>Rol</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="tablaUsuarios">
            @forelse ($usuarios as $usuario)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $usuario->name }}</td>
                <td>{{ $usuario->user }}</td>
                <td>
                  <span class="badge-rol">
                    {{ $usuario->roles->first()->name ?? 'Sin rol' }}
                  </span>
                </td>
                <td>
                  <span class="badge-status {{ $usuario->status ? 'activo' : 'inactivo' }}">
                    {{ $usuario->status ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>

                <td>
                  <button class="btn btn-icon-ghost btn-editar" type="button" data-bs-toggle="modal"
                    data-bs-target="#usuarioModal" data-id="{{ $usuario->id }}" data-nombre="{{ $usuario->name }}"
                    data-usuario="{{ $usuario->user }}" data-rol="{{ $usuario->roles->first()->name }}"
                    data-status="{{ $usuario->status }}">
                    <i data-lucide="edit-2"></i>
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center">No hay usuarios registrados</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>


  {{-- MODAL CREAR / EDITAR USUARIO --}}
  <div class="modal fade" id="usuarioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <form id="formUsuario" method="POST" action="{{ route('usuario.store') }}">
        @csrf
        <input type="hidden" id="usuario_id" name="id">

        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="usuarioModalLabel">Nuevo Usuario</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <p class="modal-subtitle">Complete los datos del usuario</p>

            <div class="row g-3">

              <div class="col-md-6">
                <label for="name" class="form-label">Nombre Completo*</label>
                <input type="text" class="form-control" id="name" name="name" required>
              </div>

              <div class="col-md-3">
                <label>Usuario *</label>
                <input type="text" id="user" name="user" class="form-control @error('user') is-invalid @enderror"
                  value="{{ old('user') }}" onkeyup="verificarUsuarioDisponible()" required>

                <div id="user-feedback" class="invalid-feedback d-none"></div>
              </div>

              <div class="col-md-3">
                <label for="rol" class="form-label">Rol *</label>
                <select id="rol" name="rol" class="form-select" required>
                  <option value="Administrativo">Administrativo</option>
                  <option value="Medico">Médico</option>
                  <option value="Enfermera">Enfermera</option>
                  <option value="Soporte">Soporte</option>
                </select>
              </div>

              <div class="col-md-2">
                <label for="status" class="form-label">Estado *</label>
                <select id="status" name="status" class="form-select" required>
                  <option value="activo">Activo</option>
                  <option value="inactivo">Inactivo</option>
                </select>
              </div>

              <div class="col-md-4">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password">
                <small class="text-muted" id="passwordNote">Requerida para crear usuario</small>
              </div>

              <div class="col-md-4">
                <label for="confirmPassword" class="form-label">Confirmar Contraseña</label>
                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword">
                <div class="invalid-feedback">Las contraseñas no coinciden</div>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar Usuario</button>
          </div>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('scripts')

  <script>
    let timeout;

    // Verificar disponibilidad de usuario 

    function verificarUsuarioDisponible() {
      clearTimeout(timeout);

      timeout = setTimeout(() => {
        const user = document.getElementById('user').value;
        const feedback = document.getElementById('user-feedback');

        if (user.trim() === "") return;

        fetch(`/api/verificar-usuario?user=${encodeURIComponent(user)}`)
          .then(res => res.json())
          .then(data => {
            const input = document.getElementById('user');

            if (data.disponible) {
              input.classList.remove("is-invalid");
              input.classList.add("is-valid");
              feedback.classList.remove("d-none", "invalid-feedback");
              feedback.classList.remove("text-success");
              feedback.textContent = "Usuario disponible";

            } else {
              input.classList.remove("is-valid");
              input.classList.add("is-invalid");
              feedback.classList.remove("d-none", "valid-feedback");
              feedback.classList.remove("text-success");
              feedback.textContent = "Usuario no disponible";
            }
          });
      }, 300); // debounce
    }

  </script>

  <!-- Fin verificar Usuario Disponible -->

  <!-- Validación de Contraseña y confirm Password-->
  <script>
    document.addEventListener("DOMContentLoaded", () => {

      const form = document.getElementById("formUsuario"); // cambia el ID por el de tu formulario real
      const password = document.getElementById("password");
      const confirmPassword = document.getElementById("confirmPassword");

      form.addEventListener("submit", function (e) {

        // Si ambos campos tienen algo, validamos
        if (password.value !== "" || confirmPassword.value !== "") {

          if (password.value !== confirmPassword.value) {

            confirmPassword.classList.add("is-invalid");

            e.preventDefault(); // Detiene el envío del formulario
            return false;
          }
        }
        // Si todo bien, quitar errores
        confirmPassword.classList.remove("is-invalid");
      });

    });
  </script>

  <!-- Fin Validación de Contraseña y confirm Password -->

  
  <!-- Modulo de editar -->

  <script>

    document.addEventListener('DOMContentLoaded', function () {
      const buttonsEditar = document.querySelectorAll('.btn-editar');
      const modal = document.getElementById('usuarioModal');

      // Cargar datos al editar
      buttonsEditar.forEach(btn => {
        btn.addEventListener('click', function () {
          document.getElementById('usuario_id').value = this.dataset.id;
          document.getElementById('name').value = this.dataset.nombre;
          document.getElementById('user').value = this.dataset.usuario;
          
          document.getElementById('status').value = this.dataset.status == '1' ? 'activo' : 'inactivo';

          document.getElementById('password').value = '';
          document.getElementById('confirmPassword').value = '';
          document.getElementById('passwordNote').textContent = '(dejar vacío para mantener)';
          document.getElementById('usuarioModalLabel').textContent = 'Editar Usuario';
        });
      });

      // Reset al cerrar
      modal.addEventListener('hidden.bs.modal', function () {
        nuevoUsuario();

        // Limpiar validaciones
        document.getElementById('user').classList.remove("is-valid", "is-invalid");
        document.getElementById('user-feedback').classList.add("d-none"); 
      });
    });

    function nuevoUsuario() {

      document.getElementById('formUsuario').reset();
      document.getElementById('usuario_id').value = '';
      document.getElementById('passwordNote').textContent = 'Requerida para crear usuario';
      document.getElementById('usuarioModalLabel').textContent = 'Nuevo Usuario';
    }

    // Filtro de usuarios

    function filtrarUsuarios() {
      const filtro = document.getElementById('buscador').value.toLowerCase();
      const filas = document.querySelectorAll('#tablaUsuarios tr');
      filas.forEach(fila => {
        const texto = fila.textContent.toLowerCase();
        fila.style.display = texto.includes(filtro) ? '' : 'none';
      });
    }

    // Fin filtro de usuarios
  </script>

@endpush