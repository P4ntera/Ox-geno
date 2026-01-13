<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('login'); // Si no está autenticado, muestra la vista de login               
    }

    public function login(Request $request)
    {
        //dd($request);
        $credentials = $request->validate([
            'user' => 'required|string|max:50',
            'password' => 'required|string|max:50',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // seguridad
            return redirect()->route('dashboard.index');
        }

        return back()->withErrors([
            'message' => 'Usuario o contraseña incorrectos.',
        ])->withInput();
    }
    public function logout()
    {
        // Desconectar al usuario
        Auth::logout();

        // Redirigir al login
        return redirect()->route('login');
    }
}
