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
            'cart.*.id' => 'required|exists:products,produkID',
            'cart.*.quantity' => 'required|integer|min:1',
        ]);

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

            $today = now()->toDateString();
            $lastOrder = Penjualan::whereDate('created_at', $today)
                ->whereNotNull('kodePesanan')
                ->orderBy('penjualanID', 'desc')
                ->first();

            $sequence = $lastOrder ? (int)substr($lastOrder->kodePesanan, -3) + 1 : 1;
            $kodePesanan = 'ORD-' . now()->format('Ymd') . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);

            // 4. Simpan penjualan
            $penjualan = Penjualan::create([
                'pelangganID' => $pelanggan->pelangganID,
                'totalHarga' => $totalHarga,
                'kodePesanan' => $kodePesanan,
                'status' => 'pending',
            ]);

            // 5. Simpan detail penjualan & kurangi stock
            foreach ($request->cart as $item) {
                $product = Product::findOrFail($item['id']);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok tidak mencukupi untuk {$product->namaProduk}");
                }

                $product->decrement('stock', $item['quantity']);

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
                'message' => 'Order berhasil!',
                'kodePesanan' => $kodePesanan, // 👈 kirim ke frontend
                'penjualanID' => $penjualan->penjualanID,
                'redirect' => route('kasir.orderMenu'),
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
