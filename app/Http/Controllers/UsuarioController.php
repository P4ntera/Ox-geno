<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;
use Spatie\Permission\Commands\AssignRole;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index()
    { 
        $usuarios = User::with('roles')->get(); // importante
        //dd($usuarios);
        return view('usuarios', compact('usuarios'));

    }

    public function store(Request $request)
    {
        //dd($request);

        $validatedData = $request->validate(
            [
                'name' => 'required',
                'user' => [
                    'required',
                    'string',
                    Rule::unique('users', 'user')->ignore($request->id),
                ],
                'password' => 'nullable|min:6',
                'status' => 'required'
            ],
            [
                'user.unique' => 'Este usuario ya se encuentra registrado, intente otro.'
            ]
        );

        if ($request->id) {
            $user = User::find($request->id, ['*']);

            //dd($user);

            //si el request tiene password, actualizarlo si no, mantener el actual
            if ($request->filled('password')) {
                $validatedData['password'] = Hash::make($request->password);
            } else {
                // Mantener la contraseña actual si no se envió
                unset($validatedData['password']);
            }

            $user->update($validatedData); //atualizar usuario

            $user->syncRoles([$request->rol]); //asignar rol

            return redirect()
                ->route('usuario.index')
                ->with('success', 'Paciente guardado exitosamente.');

        } else {
            // Crear nuevo usuario
            $validatedData['password'] = Hash::make($validatedData['password']); //hashear password
            $user = User::create($validatedData); //crear usuario
            $user->assignRole($request->rol); //asignar rol

        }

        return redirect()
            ->route('usuario.index')
            ->with('success', 'Paciente guardado exitosamente.');
    }
}
