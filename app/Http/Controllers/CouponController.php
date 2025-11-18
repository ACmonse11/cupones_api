<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::with('category')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($coupon) {

                // URL absoluta siempre
                if ($coupon->image && !str_contains($coupon->image, 'http')) {
                    $coupon->image = url($coupon->image);
                }

                return $coupon;
            });

        return response()->json($coupons);
    }

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
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data['discount'] = $data['discount'] ?? 0;

        // Código automático
        do {
            $generatedCode = strtoupper(substr($data['title'], 0, 3)) . rand(100, 999);
        } while (Coupon::where('code', $generatedCode)->exists());

        $data['code'] = $data['code'] ?: $generatedCode;

        // Guardar imagen
        if ($request->hasFile('image')) {

            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();

            // Guardar físicamente
            $file->move(public_path('uploads/coupons'), $filename);

            // Guardar ruta relativa
            $data['image'] = '/image/coupon/' . $filename;
        }

        $coupon = Coupon::create($data);
        $coupon->load('category');

        // URL absoluta final
        if ($coupon->image) {
            $coupon->image = url($coupon->image);
        }

        return response()->json([
            'message' => 'Cupón creado correctamente ✅',
            'data' => $coupon
        ], 201);
    }

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

        $data['discount'] = $data['discount'] ?? $coupon->discount ?? 0;

        // Reemplazar imagen
        if ($request->hasFile('image')) {

            // Borrar anterior si existe
            if ($coupon->image && str_contains($coupon->image, '/uploads/coupons/')) {

                $localPath = public_path($coupon->image);

                if (file_exists($localPath)) {
                    unlink($localPath);
                }
            }

            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('uploads/coupons'), $filename);

            $data['image'] = '/image/coupon/' . $filename;

        } else {
            $data['image'] = $coupon->image;
        }

        $coupon->update($data);
        $coupon->load('category');

        // URL absoluta
        if ($coupon->image) {
            $coupon->image = url($coupon->image);
        }

        return response()->json([
            'message' => 'Cupón actualizado correctamente ✅',
            'data' => $coupon
        ]);
    }

    public function destroy(Coupon $coupon)
    {
        if ($coupon->image && str_contains($coupon->image, '/uploads/coupons/')) {

            $localPath = public_path($coupon->image);

            if (file_exists($localPath)) {
                unlink($localPath);
            }
        }

        $coupon->delete();

        return response()->json(['message' => 'Cupón eliminado ✅']);
    }
}
