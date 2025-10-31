<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Exception;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::orderBy('id', 'desc')->get());
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'required|email|unique:users',
                'password' => 'nullable|string|min:6',
                'role' => 'required|in:Administrador,Editor,Cliente',
                'status' => 'required|in:Activo,Inactivo',
            ]);

            // Si no se manda contraseña, se asigna una por defecto
            $validated['password'] = isset($validated['password'])
                ? Hash::make($validated['password'])
                : Hash::make('123456');

            $user = User::create($validated);

            return response()->json([
                'message' => 'Usuario creado correctamente',
                'user' => $user,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Error de validación',
                'details' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error al guardar usuario',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:100',
                'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
                'role' => 'sometimes|required|in:Administrador,Editor,Cliente',
                'status' => 'sometimes|required|in:Activo,Inactivo',
            ]);

            $user->update($validated);

            return response()->json([
                'message' => 'Usuario actualizado correctamente',
                'user' => $user,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Error de validación',
                'details' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error al actualizar usuario',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            return response()->json(['message' => 'Usuario eliminado correctamente']);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error al eliminar usuario',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
