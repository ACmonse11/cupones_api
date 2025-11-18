<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        return Banner::orderBy('id', 'desc')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5000',
            'title' => 'nullable|string'
        ]);

        $fileName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('uploads/banners'), $fileName);

        $imageUrl = config('app.url') . '/uploads/banners/' . $fileName;

        $banner = Banner::create([
            'title' => $request->title,
            'image_url' => $imageUrl,
            'active' => true
        ]);

        return response()->json([
            "message" => "Banner creado",
            "banner" => $banner
        ]);
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        if ($request->hasFile('image')) {
            $fileName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/banners'), $fileName);

            $banner->image_url = config('app.url') . '/uploads/banners/' . $fileName;
        }

        $banner->title = $request->title ?? $banner->title;
        $banner->active = $request->active ?? $banner->active;

        $banner->save();

        return $banner;
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->delete();

        return response()->json(["message" => "Banner eliminado"]);
    }

    // endpoint para obtener banners activos
    public function activeBanners()
    {
        return Banner::where('active', true)->orderBy('id', 'desc')->get();
    }
}
