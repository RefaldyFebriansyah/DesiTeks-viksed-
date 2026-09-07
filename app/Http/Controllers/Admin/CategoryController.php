<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\AuditLog;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('fabrics')->orderBy('nama_kategori')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $existingCategories = Category::withCount('fabrics')->orderBy('nama_kategori')->get();
        $totalFabricsCount = \App\Models\Fabric::count();
        return view('admin.categories.create', compact('existingCategories', 'totalFabricsCount'));
    }

    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->validated());
        AuditLog::create([
            'user_id'   => Auth::id(),
            'aktivitas' => "Menambah kategori: {$category->nama_kategori}",
            'model'     => 'Category',
            'model_id'  => $category->id,
        ]);
        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori {$category->nama_kategori} berhasil ditambahkan.");
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->validated());
        AuditLog::create([
            'user_id'   => Auth::id(),
            'aktivitas' => "Mengubah kategori: {$category->nama_kategori}",
            'model'     => 'Category',
            'model_id'  => $category->id,
        ]);
        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori berhasil diperbarui.");
    }

    public function destroy(Category $category)
    {
        if ($category->fabrics()->count() > 0) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki data kain.');
        }
        $nama = $category->nama_kategori;
        $category->delete();
        AuditLog::create([
            'user_id'   => Auth::id(),
            'aktivitas' => "Menghapus kategori: {$nama}",
        ]);
        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori {$nama} berhasil dihapus.");
    }
}
