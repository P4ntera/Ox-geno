<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - Consultorio Médico</title>
    <link rel="stylesheet" href="{{ asset('css\login.css') }}">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
    <div class="login-container">
        <div class="login-card">

            <div class="icon-circle">
                <input type="image" src="img\oxigeno.png" alt="" width="60px">
            </div>

            <h2>Sistema de Oxígeno</h2>
            <p class="subtitle">Ingrese sus credenciales para continuar</p>

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <label for="usuario">Usuario</label>
                <div class="input-group">
                    <i class='bx bxs-user'></i>
                    <input type="text" name="user" id="user" placeholder="Ingrese su usuario" value="{{ old(key: 'user') }}" required>
                </div>

                <div class="input-group">
                    <i class='bx bxs-lock-alt'></i>
                    <input type="password" name="password" id="password" placeholder="Ingrese su contraseña" required>
                </div>

                <button type="submit" class="btn">Iniciar Sesión</button>
            </form>

            <div class="test-users">
                <ul class="test-users-list text-center">
                    <li><b>La información manejada es confidencial.</b></li>

                    <li><b>El acceso no autorizado está prohibido.</b></li>

                    <li><b>Uso sujeto a normativas del centro de salud.</b></li>
                </ul>
            </div>
        </div>
    </div>
    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif
    <script src="{{ asset('/js/validaciones.js') }}"></script>
    <script src="{{ asset('/js/alertas.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if ($errors->has('message'))
    <script>
        showAlert(); //llama la alerta desde el js
    </script>
    @endif
</body>

</html>