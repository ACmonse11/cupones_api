<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function enviar(Request $request)
    {
        // Validación
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'email'  => 'required|email',
            'asunto' => 'nullable|string|max:255',
            'mensaje'=> 'required|string',
        ]);

        // Enviar correo sin vista (más seguro y simple)
        Mail::raw("
Nombre: {$data['nombre']}
Correo: {$data['email']}
Asunto: {$data['asunto']}
Mensaje:
{$data['mensaje']}
        ", function ($msg) use ($data) {
            $msg->to('tu_correo_real@gmail.com'); // <-- CAMBIA A TU CORREO REAL
            $msg->subject($data['asunto'] ?? 'Nuevo mensaje de contacto');
        });

        return response()->json([
            'ok' => true,
            'mensaje' => 'Mensaje enviado correctamente.'
        ], 200);
    }
}
