<?php

namespace App\Http\Controllers;

use App\Models\Store;

class PublicStoreController extends Controller
{
    public function show($slug)
    {
        $store = Store::where('slug', $slug)
            ->with(['products', 'categories'])
            ->firstOrFail();

        return view('store.public', compact('store'));
    }
}
