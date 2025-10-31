<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CouponController extends Controller
{
    /**
     * Muestra todos los cupones con su categoría e imagen completa.
     */
    public function index()
    {
        $coupons = Coupon::with('category')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($coupon) {
                // ✅ Convertir la ruta interna en URL completa
                if ($coupon->image) {
                    $coupon->image = asset('storage/' . $coupon->image);
                }
                return $coupon;
            });

        return response()->json($coupons);
    }

    /**
     * Guarda un nuevo cupón.
     */
    public function store(Request $request)
{
    $data = $request->validate([
        'title' => 'required|string|max:255',
        'code' => 'nullable|string|max:100|unique:coupons',
        'description' => 'nullable|string',
        'discount' => 'nullable|integer|min:0|max:100',
        'expiration_date' => 'nullable|date',
        'status' => 'required|in:Activo,Inactivo',
        'category_id' => 'nullable|exists:categories,id',
        'image' => 'nullable',
    ]);

    // ✅ Si no se envía descuento, asignar 0
    $data['discount'] = $data['discount'] ?? 0;

    // ✅ Generar código automático si no se envía
    do {
        $generatedCode = strtoupper(substr($data['title'], 0, 3)) . rand(100, 999);
    } while (Coupon::where('code', $generatedCode)->exists());

    if (empty($data['code'])) {
        $data['code'] = $generatedCode;
    }

    // ✅ Guardar imagen si se sube
    if ($request->hasFile('image') && $request->file('image')->isValid()) {
        $path = $request->file('image')->store('coupons', 'public');
        $data['image'] = $path;
    }
    // ✅ Si viene una URL o texto desde el frontend
    elseif ($request->filled('image') && filter_var($request->image, FILTER_VALIDATE_URL)) {
        $data['image'] = $request->image;
    }
    // ✅ Si Vue envía el archivo pero Laravel no lo detecta como "file"
    elseif ($request->has('image') && is_string($request->image)) {
        $data['image'] = $request->image;
    }

    $coupon = Coupon::create($data)->load('category');

    // ✅ Agregar URL completa si hay imagen guardada
    if ($coupon->image && !str_starts_with($coupon->image, 'http')) {
        $coupon->image = asset('storage/' . $coupon->image);
    }

    return response()->json([
        'message' => 'Cupón creado correctamente',
        'data' => $coupon,
    ], 201);
}


    /**
     * Muestra un cupón por su ID.
     */
    public function show(Coupon $coupon)
    {
        $coupon->load('category');
        if ($coupon->image) {
            $coupon->image = asset('storage/' . $coupon->image);
        }
        return response()->json($coupon);
    }

    /**
     * Actualiza un cupón existente.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:100|unique:coupons,code,' . $coupon->id,
            'description' => 'nullable|string',
            'discount' => 'nullable|integer|min:0|max:100',
            'expiration_date' => 'nullable|date',
            'status' => 'required|in:Activo,Inactivo',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // ✅ Si no se envía descuento, conservar el anterior o usar 0
        if (!isset($data['discount']) || $data['discount'] === null) {
            $data['discount'] = $coupon->discount ?? 0;
        }

        // ✅ Si no se envía código nuevo, conservar el actual
        if (empty($data['code'])) {
            $data['code'] = $coupon->code;
        }

        // ✅ Reemplazar imagen si se envía una nueva
        if ($request->hasFile('image')) {
            if ($coupon->image && Storage::disk('public')->exists($coupon->image)) {
                Storage::disk('public')->delete($coupon->image);
            }
            $path = $request->file('image')->store('coupons', 'public');
            $data['image'] = $path;
        }

        $coupon->update($data);

        // ✅ Adjuntar URL completa
        if ($coupon->image) {
            $coupon->image = asset('storage/' . $coupon->image);
        }

        return response()->json([
            'message' => 'Cupón actualizado correctamente',
            'data' => $coupon->load('category'),
        ]);
    }

    /**
     * Elimina un cupón y su imagen asociada.
     */
    public function destroy(Coupon $coupon)
    {
        if ($coupon->image && Storage::disk('public')->exists($coupon->image)) {
            Storage::disk('public')->delete($coupon->image);
        }

        $coupon->delete();

        return response()->json(['message' => 'Cupón eliminado correctamente']);
    }
}
