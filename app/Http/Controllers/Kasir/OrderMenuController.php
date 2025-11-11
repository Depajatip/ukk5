<?php

namespace App\Http\Controllers\Kasir;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Product;
use DB;


use App\Http\Controllers\Controller;

class OrderMenuController extends Controller
{
    public function index()
    {
        $categories = \App\Models\Product::select('category')->distinct()->get();

        $products = \App\Models\Product::all();

        return view('kasir.orderMenu', compact('categories', 'products'));
    }
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'namaPelanggan' => 'required|string|max:255',
            'alamat' => 'required|string',
            'noTelpPelanggan' => 'required|string',
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|exists:products,produkID', // pastikan produk ada
            'cart.*.quantity' => 'required|integer|min:1',
        ]);

        // Mulai transaksi (agar rollback jika gagal)
        DB::beginTransaction();

        try {
            // 1. Simpan pelanggan
            $pelanggan = Pelanggan::create([
                'namaPelanggan' => $request->namaPelanggan,
                'alamat' => $request->alamat,
                'noTelpPelanggan' => $request->noTelpPelanggan,
            ]);

            // 2. Hitung total harga
            $totalHarga = 0;
            foreach ($request->cart as $item) {
                $product = Product::findOrFail($item['id']);
                $totalHarga += $product->harga * $item['quantity'];
            }

            // 3. Simpan penjualan
            $penjualan = Penjualan::create([
                'pelangganID' => $pelanggan->pelangganID,
                'totalHarga' => $totalHarga,
                'tanggalPenjualan' => now(),
            ]);

            // 4. Simpan detail penjualan & kurangi stock
            foreach ($request->cart as $item) {
                $product = Product::findOrFail($item['id']);

                // Cek stok cukup?
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok tidak mencukupi untuk {$product->namaProduk}");
                }

                // Kurangi stock
                $product->decrement('stock', $item['quantity']);

                // Simpan detail
                DetailPenjualan::create([
                    'penjualanID' => $penjualan->penjualanID,
                    'produkID' => $product->produkID,
                    'jumlahProduk' => $item['quantity'],
                    'subTotal' => $product->harga * $item['quantity'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil disimpan!',
                'penjualanID' => $penjualan->penjualanID,
                'redirect' => route('penjualan.list') // redirect ke list pesanan
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
