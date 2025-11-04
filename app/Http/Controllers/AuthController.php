<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Exception;

class AuthController extends Controller
{
    /**
     * Registro de usuarios
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
            ]);

            // Crear usuario con rol por defecto "Cliente"
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role ?? 'Cliente',
                'status' => 'Activo',
            ]);

            return response()->json([
                'message' => 'Usuario registrado correctamente ✅',
                'user' => $user
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error al registrar usuario ❌',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Inicio de sesión
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['Las credenciales no son correctas.'],
                ]);
            }

            if ($user->status !== 'Activo') {
                return response()->json([
                    'error' => 'Tu cuenta está inactiva. Contacta al administrador.',
                ], 403);
            }

            $user->update(['last_login' => now()]);

            $token = $user->createToken('auth_token')->plainTextToken;

            // ✅ Normalizar rol
            $role = trim(strtolower($user->role));
            $adminRoles = ['admin', 'administrator', 'administrador'];

            $normalizedRole = in_array($role, $adminRoles) ? 'admin' : 'user';

            return response()->json([
                'message' => 'Inicio de sesión correcto ✅',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $normalizedRole,
                    'status' => $user->status,
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error al iniciar sesión ❌',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cierre de sesión
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return response()->json(['message' => 'Sesión cerrada correctamente ✅']);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error al cerrar sesión ❌',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
