@extends('layouts.app')

@section('title', 'Sistema de Gestión de Oxígeno')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')

    <div class="dashboard-header">
        <div class="header-icon">
            <i data-lucide="activity"></i>
        </div>
        <div>
            <h1 class="title">Panel de Control</h1>
            <p class="subtitle">Resumen general del sistema</p>
        </div>
    </div>

    {{-- === Estadísticas === --}}
    <div class="stats-grid">

        {{-- MÉDICO --}}
        @isset($data['pacientes_conectados'])
            <div class="stat-card">
                <div class="stat-header">
                    <p class="stat-title">Pacientes Conectados</p>
                    <h3>{{ $data['pacientes_conectados'] }}</h3>
                    <div class="stat-icon bg-blue-100">
                        <i data-lucide="user-check" class="text-blue-600"></i>
                    </div>
                </div>
            </div>
        @endisset

        {{-- ENFERMERA --}}
        @isset($data['ordenes_pendientes'])
            <div class="stat-card">
                <div class="stat-header">
                    <p class="stat-title">Órdenes Pendientes</p>
                    <h3>{{ $data['ordenes_pendientes'] }}</h3>
                    <div class="stat-icon bg-yellow-100">
                        <i data-lucide="clipboard-list" class="text-yellow-600"></i>
                    </div>
                </div>
            </div>
        @endisset

        {{-- ADMIN --}}
        @isset($data['monto_hoy'])
            <div class="stat-card">
                <div class="stat-header">
                    <p class="stat-title">Monetizado Hoy</p>
                    <h3>${{ number_format($data['monto_hoy'], 2) }}</h3>
                    <div class="stat-icon bg-green-100">
                        <i data-lucide="dollar-sign" class="text-green-600"></i>
                    </div>
                </div>
            </div>
        @endisset

        {{-- SOPORTE --}}
        @isset($data['usuarios'])
            <div class="stat-card">
                <div class="stat-header">
                    <p class="stat-title">Usuarios del Sistema</p>
                    <h3>{{ $data['usuarios'] }}</h3>
                    <div class="stat-icon bg-purple-100">
                        <i data-lucide="users" class="text-purple-600"></i>
                    </div>
                </div>
            </div>
        @endisset

    </div>

    {{-- === Grids inferior === --}}
    <div class="info-grid">

        {{-- Guía rápida --}}
        <div class="info-card">
            <h3 class="info-title">Guía Rápida</h3>

            <div class="steps">

                <div class="step">
                    <div class="step-number">1</div>
                    <div>
                        <p class="step-title">Registrar Pacientes</p>
                        <p class="step-desc">Agregue los datos en la sección "Pacientes".</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">2</div>
                    <div>
                        <p class="step-title">Registrar Consumo</p>
                        <p class="step-desc">Ingrese el consumo en "Consumo O₂".</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">3</div>
                    <div>
                        <p class="step-title">Ver Reportes</p>
                        <p class="step-desc">Consulte reportes por paciente o fecha.</p>
                    </div>
                </div>

            </div>
        </div>

        {{-- Sistema --}}
        <div class="info-card">
            <h3 class="info-title">Información del Sistema</h3>

            <div class="info-row">
                <span class="label">Versión del Sistema</span>
                <span>1.0.0</span>
            </div>

            <div class="info-row">
                <span class="label">Última Actualización</span>
                <span>Noviembre 2025</span>
            </div>

            <div class="info-row">
                <span class="label">Estado del Sistema</span>
                <span class="status-active">● Operativo</span>
            </div>
        </div>

    </div>

@endsection