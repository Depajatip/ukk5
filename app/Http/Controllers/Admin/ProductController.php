<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function manageProduct()
    {
        $products = Product::all();
        return view('admin.manageProduct', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'namaProduk' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'stock' => 'required|integer|min:0',
            'harga' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // maksimal 2MB
        ]);

        $imagePath = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'namaProduk' => $request->namaProduk,
            'category' => $request->category,
            'stock' => $request->stock,
            'harga' => $request->harga,
            'image' => $imagePath, // simpan path gambar
        ]);


        return redirect()->route('admin.manageProduct')->with('success', 'Product berhasil ditambahkan!');
    }
    public function destroyProduct($produkID)
    {
        Product::findOrFail($produkID)->delete();
        return redirect()->route('admin.manageProduct')->with('success', 'Product berhasil dihapus');
    }
    public function update(Request $request, $produkID)
{
    $product = Product::findOrFail($produkID);
    $product->namaProduk = $request->namaProduk;
    $product->category = $request->category;
    $product->harga = $request->harga;
    $product->stock = $request->stock;
    $product->save();

    return redirect()->route('admin.manageProduct')->with('success', 'Product berhasil diupdate');
}

}
