<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Logo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LogoController extends Controller
{
    public function index()
    {
        $logos = Logo::latest()->get();
        return view('admin.logos.index', compact('logos'));
    }

    public function create()
    {
        return view('admin.logos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $imagePath = $request->file('image')->store('logos', 'public');

        Logo::create([
            'image' => $imagePath
        ]);

        return redirect()->route('admin.logos.index')
                        ->with('success', 'Logo đã được thêm thành công!');
    }

    public function show(Logo $logo)
    {
        return view('admin.logos.show', compact('logo'));
    }

    public function edit(Logo $logo)
    {
        return view('admin.logos.edit', compact('logo'));
    }

    public function update(Request $request, Logo $logo)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ
            if (Storage::exists('public/' . $logo->image)) {
                Storage::delete('public/' . $logo->image);
            }

            // Lưu ảnh mới
            $imagePath = $request->file('image')->store('logos', 'public');
            $logo->update(['image' => $imagePath]);
        }

        return redirect()->route('admin.logos.index')
                        ->with('success', 'Logo đã được cập nhật thành công!');
    }

    public function destroy(Logo $logo)
    {
        // Xóa file ảnh
        if (Storage::exists('public/' . $logo->image)) {
            Storage::delete('public/' . $logo->image);
        }

        $logo->delete();

        return redirect()->route('admin.logos.index')
                        ->with('success', 'Logo đã được xóa thành công!');
    }
}
