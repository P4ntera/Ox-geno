<?php

use App\Http\Controllers\PacienteController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\CentroSaludController;
use App\Http\Controllers\ArsController;
use App\Http\Controllers\HabitacionController;
use App\Http\Controllers\ConsumoOxigenoController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrdenOxigenoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ReporteConsumoControllerl;
use App\Http\Controllers\EstadoConsumoController;
use Illuminate\Http\Request;
use Symfony\Component\Routing\Route as RoutingRoute;

//----------------------------------------------------------------------------------------------------------------------------
// LOGIN

Route::middleware(['guest', 'no.cache'])->group(function () {

    Route::get('/', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

});

Route::middleware('auth')->group(function () {

    Route::post('logout', [LoginController::class, 'logout'])
        ->name('logout');
});

//----------------------------------------------------------------------------------------------------------------------------
// Dashboard

Route::middleware(['auth', 'no.cache'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

});

//----------------------------------------------------------------------------------------------------------------------------
// ====== PACIENTES ======

Route::middleware(['auth', 'no.cache'])->group(function () {

    Route::get('/pacientes', [PacienteController::class, 'index'])->name('pacientes.index');

    Route::post('/pacientes/store', [PacienteController::class, 'store'])->name('pacientes.store');
});

//----------------------------------------------------------------------------------------------------------------------------
// ====== REPORTES ======

Route::middleware(['auth', 'no.cache'])->group(function () {

    Route::get('/consumo-oxigeno', [ReporteController::class, 'index'])
        ->name('reportes.index');
});


//----------------------------------------------------------------------------------------------------------------------------
// ====== USUARIOS =====//

Route::middleware(['auth', 'no.cache'])->group(function () {

    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuario.index');

    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuario.store');

    Route::get('/api/verificar-usuario', function (Request $request) {
        $existe = \App\Models\User::where('user', $request->user)->exists();

        return ['disponible' => !$existe];
    });
});

//----------------------------------------------------------------------------------------------------------------------------
// ====== ASEGURADORAS ======

Route::middleware(['auth', 'no.cache'])->group(function () {

    Route::get('/ars', [ArsController::class, 'index'])->name('ars.index');

    Route::post('/ars/store', [ArsController::class, 'store'])->name('ars.store');

    Route::delete('/ars/delete', [ArsController::class, 'delete'])->name('ars.destroy');
});

//----------------------------------------------------------------------------------------------------------------------------
// ====== CENTRO DE SALUD ======

Route::middleware(['auth', 'no.cache'])->group(function () {

    Route::get('/centros-salud', [CentroSaludController::class, 'index'])->name('centrosalud.index');

    Route::post('/centros-salud/store', [CentroSaludController::class, 'store'])->name('centrosalud.store');
});

//---------------------------------------------------------------------------------------------------------------------------
// ====== HABITACIONES ======

Route::middleware(['auth', 'no.cache'])->group(function () {

    Route::get('/habitaciones', [HabitacionController::class, 'index'])->name('habitaciones.index');

    Route::post('/habitaciones/store', [HabitacionController::class, 'store'])->name('habitaciones.store');
});
//----------------------------------------------------------------------------------------------------------------------------
// ====== CONSUMO O2 ======

Route::middleware(['auth', 'no.cache'])->group(function () {

    Route::get('/consumo', [ConsumoOxigenoController::class, 'index'])->name('consumo.index');

    Route::post('/store', [ConsumoOxigenoController::class, 'store'])->name('consumo.store');

    Route::post('/consumo/{id}/finalizar', [ConsumoOxigenoController::class, 'finalizar'])
        ->name('consumo.finalizar');

});

//----------------------------------------------------------------------------------------------------------------------------
// ====== ESTADO CONSUMO ======

Route::middleware(['auth', 'no.cache'])->group(function () {

    Route::get('/estado', [EstadoConsumoController::class, 'index'])->name('estado.index');

    Route::post('/estado/store', [EstadoConsumoController::class, 'store'])->name('estado.store');

});

//----------------------------------------------------------------------------------------------------------------------------
// ====== Orden Oxigeno ======

Route::middleware(['auth', 'no.cache'])->group(function () {

    Route::get('/ordenes-oxigeno', [OrdenOxigenoController::class, 'index'])->name('ordenes.index');

    Route::post('/ordenes-oxigeno', [OrdenOxigenoController::class, 'store'])->name('ordenes.store');

    Route::put('/ordenes/{id}/completar', [OrdenOxigenoController::class, 'completar'])->name('ordenes.completar');

    Route::put('/ordenes/{id}/cancelar', [OrdenOxigenoController::class, 'cancelar'])->name('ordenes.cancelar');

});

//-------------------------------------------------------------------------------------------------------------------------------
//===== Estado Consumo y Detalles ======
Route::middleware(['auth', 'no.cache'])->group(function () {

    Route::get(
        '/estado-consumo',
        [EstadoConsumoController::class, 'index']
    )->name('estado_consumo.index');

    Route::post('/estado-consumo/seleccionar', [EstadoConsumoController::class, 'seleccionarPaciente'])
        ->name('estado_consumo.seleccionar');

    Route::post('/estado-consumo/cerrar', [EstadoConsumoController::class, 'cerrar'])
        ->name('estado_consumo.cerrar');

    Route::get(
        '/estado-consumo/resumen',
        [EstadoConsumoController::class, 'resumen']
    )->name('estado_consumo.resumen');

});

