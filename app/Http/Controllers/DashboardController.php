<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\User;
use App\Models\OrdenOxigeno;
use App\Models\ConsumoOxigeno;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $rol = $user->getRoleNames()->first(); // Spatie package
  
        if (!$user) {
            return redirect()->route('login');
        }

        $data = [];

        switch ($rol) {

            // case 'Medico':
            //     $data['pacientes_conectados'] = Paciente::where('estado', 'Activo')->count();
            //     break;

            // case 'Enfermera':
            //     $data['ordenes_pendientes'] = OrdenOxigeno::where('estado', 'Pendiente')->count();
            //     break;

            case 'Administrativo':
                //$data['pacientes_conectados'] = Paciente::where('estado', 'Activo')->count();
                $data['ordenes_pendientes'] = OrdenOxigeno::where('estado', 'Activa')->count();
                $data['monto_hoy'] = ConsumoOxigeno::whereDate('fecha_inicio', today())
                    ->sum('costo_total');
                break;

            // case 'Soporte':
            //     $data['usuarios'] = User::count();
            //     break;
        }

        return view('dashboard', compact('rol', 'data'));
    }
}
