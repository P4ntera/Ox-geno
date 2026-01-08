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

    {{-- Pacientes --}}
    <div class="stat-card">
        <div class="stat-header">
            <p class="stat-title">Pacientes Registrados</p>
            <h3>0</h3>
            <div class="stat-icon bg-blue-100">
                <i data-lucide="user-plus" class="text-blue-600"></i>
            </div>
            
        </div>
   
    </div>

    {{-- Usuarios --}}
    <div class="stat-card">
        <div class="stat-header">
            <p class="stat-title">Usuarios del Sistema</p>
            <div class="stat-icon bg-green-100">
                <i data-lucide="users" class="text-green-600"></i>
            </div>
        </div>
       
    </div>

    {{-- Consumo Hoy --}}
    <div class="stat-card">
        <div class="stat-header">
            <p class="stat-title">Consumo Hoy (Litros)</p>
            <div class="stat-icon bg-purple-100">
                <i data-lucide="wind" class="text-purple-600"></i>
            </div>
        </div>
       
    </div>

    {{-- Costo Hoy --}}
    <div class="stat-card">
        <div class="stat-header">
            <p class="stat-title">Costo Estimado Hoy</p>
            <div class="stat-icon bg-orange-100">
                <i data-lucide="trending-up" class="text-orange-600"></i>
            </div>
        </div>
    </div>

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
