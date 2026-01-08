<?php

namespace App\Http\Controllers;

use App\Models\Ars;
use Illuminate\Http\Request;

class ArsController extends Controller
{
    public function index()
    {
        $arsList = Ars::orderBy('id_ars', 'desc')->get();
        return view('Ars', compact('arsList'));
    }

    public function store(Request $request)
    {
        //dd($request);

        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'precio_litro' => 'required|numeric|min:0',
        ]);

        //dd($validated);

        // Si viene id → actualizar
        if ($request->id_ars) {
            $ars = Ars::findOrFail($request->id_ars);
            $ars->update($validated);

            return back()->with('success', 'ARS actualizado correctamente.');
        }

        // Crear nuevo
        Ars::create($validated);

        return back()->with('success', 'ARS creado correctamente.');
    }
    public function delete($id)
    {

        //dd($id);
        Ars::where('id_ars', $id)->delete();

        return back()->with('success', 'ARS eliminado.');
    }
}
