<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Sistema de Gestión de Oxígeno')</title>

    {{-- Tema y Layout --}}
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>

    @stack('styles')
    @stack('scripts')

</head>
<script>
    function toggleDropdown(id) {
        const submenu = document.getElementById(id);
        const container = submenu.parentElement;

        submenu.classList.toggle('open');
        container.classList.toggle('open');
    }
</script>

<body>

    <div class="layout-container">

        {{-- Sidebar --}}
        <aside class="sidebar">

            <div class="sidebar-header">
                <div class="header-icon">
                    <i data-lucide="activity"></i>
                </div>
                <div>
                    <h2 class="title">Sistema O₂</h2>
                    <p class="subtitle">Gestión de Oxígeno</p>
                </div>
            </div>

            <div class="user-info">
                <p class="user-name">{{ Auth::user()->name }}</p>
                <span class="role-badge admin">{{ Auth::user()->getRoleNames()->first() }}</span>
            </div>

            <nav class="menu-list">

                <a href="{{ route('dashboard') }}"
                    class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i data-lucide="layout-dashboard"></i> Inicio
                </a>

                <a href="{{ route('ordenes.index') }}"
                    class="menu-item {{ request()->routeIs('ordenes.*') ? 'active' : '' }}">
                    <i data-lucide="clipboard-list"></i> Órdenes O₂
                </a>

                <a href="{{ route('consumo.index') }}"
                    class="menu-item {{ request()->routeIs('consumo.*') ? 'active' : '' }}">
                    <i data-lucide="wind"></i> Consumo O₂
                </a>

                <a href="{{ route('pacientes.index') }}"
                    class="menu-item {{ request()->routeIs('pacientes.*') ? 'active' : '' }}">
                    <i data-lucide="user-plus"></i> Pacientes
                </a>

                <a href="{{ route('reportes.index') }}"
                    class="menu-item {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                    <i data-lucide="file-text"></i> Reportes
                </a>

                <a href="{{ route('usuario.index') }}"
                    class="menu-item {{ request()->routeIs('usuario.*') ? 'active' : '' }}">
                    <i data-lucide="users"></i> Usuarios
                </a>

                <a href="{{ route('ars.index') }}" class="menu-item {{ request()->routeIs('ars.*') ? 'active' : '' }}">
                    <i data-lucide="shield"></i> Aseguradoras
                </a>

                <div class="menu-dropdown">

                    <a class="menu-item dropdown-toggle-btn" onclick="toggleDropdown('centrosDropdown')">
                        <i data-lucide="building-2"></i>
                        Centro de Salud
                        <i data-lucide="chevron-down" class="dropdown-arrow"></i>
                    </a>

                    <div id="centrosDropdown" class="dropdown-submenu 
        {{ request()->routeIs('centrosalud.*') || request()->routeIs('habitaciones.*') ? 'open' : '' }}">

                        <a href="{{ route('centrosalud.index') }}"
                            class="submenu-item {{ request()->routeIs('centrosalud.*') ? 'active' : '' }}">
                            Gestión Centros
                        </a>

                        <a href="{{ route('habitaciones.index') }}"
                            class="submenu-item {{ request()->routeIs('habitaciones.*') ? 'active' : '' }}">
                            Habitaciones por Centro
                        </a>
                    </div>

                </div>

            </nav>

            <form action="{{ route('logout') }}" method="POST" class="logout-box">
                @csrf
                <button class="logout-btn">
                    <i data-lucide="log-out"></i>
                    Cerrar Sesión
                </button>
            </form>

        </aside>

        {{-- Contenido --}}
        <main class="main-content">
            <div class="content-wrapper">
                @yield('content')
            </div>
        </main>

    </div>

    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>