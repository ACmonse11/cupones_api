<?php

namespace App\Http\Controllers;

use App\Models\Logo;
use Illuminate\Http\Request;

class LogoController extends Controller
{
    // 🔹 Obtener todos (solo para admin)
    public function index()
    {
        return Logo::orderBy('id', 'desc')->get();
    }

    // 🔹 Crear logo nuevo
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5000',
            'title' => 'nullable|string'
        ]);

        // Guardar archivo
        $fileName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('uploads/logos'), $fileName);

        // Crear URL
        $imageUrl = config('app.url') . '/uploads/logos/' . $fileName;

        // Crear registro
        $logo = Logo::create([
            'title'     => $request->title,
            'image_url' => $imageUrl,
            'active'    => true,
        ]);

        return response()->json([
            'message' => 'Logo creado con éxito',
            'logo'    => $logo,
        ]);
    }

    // 🔹 Actualizar logo existente
    public function update(Request $request, $id)
    {
        $logo = Logo::findOrFail($id);

        // Si envió imagen, reemplazar
        if ($request->hasFile('image')) {
            $fileName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/logos'), $fileName);
            $logo->image_url = config('app.url') . '/uploads/logos/' . $fileName;
        }

        // Actualizar otros campos
        $logo->title  = $request->title  ?? $logo->title;
        $logo->active = $request->active ?? $logo->active;
        $logo->save();

        return response()->json([
            'message' => 'Logo actualizado',
            'logo'    => $logo
        ]);
    }

    // 🔹 Eliminar logo
    public function destroy($id)
    {
        $logo = Logo::findOrFail($id);
        $logo->delete();

        return response()->json(['message' => 'Logo eliminado']);
    }

    // 🔹 Obtener logos activos (para Home)
    public function activeLogos()
    {
        return Logo::where('active', true)
                   ->orderBy('id', 'desc')
                   ->get();
    }
}
