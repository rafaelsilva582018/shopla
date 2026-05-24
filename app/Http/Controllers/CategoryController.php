<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class CategoryController extends Controller
{
    public function index()
    {
        $store = Auth::user()->store;

        $categories = $store->categories()->latest()->get();

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $store = Auth::user()->store;

        Category::create([
            'store_id' => $store->id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return back()->with('success', 'Categoria criada com sucesso!');
    }

    public function destroy(Category $category)
    {
        $store = Auth::user()->store;

        abort_if($category->store_id !== $store->id, 403);

        $category->delete();

        return back()->with('success', 'Categoria removida!');
    }
    public function edit(Category $category)
    {
        $store = Auth::user()->store;

        abort_if($category->store_id !== $store->id, 403);

        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $store = Auth::user()->store;

        abort_if($category->store_id !== $store->id, 403);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return back()->with('success', 'Categoria atualizada com sucesso!');
    }
}
