<?php

namespace App\Http\Controllers\Kasir;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\Product;
use App\Models\DetailPenjualan;

use App\Http\Controllers\Controller;


class PaymentController extends Controller
{
    // Ambil data pesanan untuk modal
    public function show($penjualanID)
{
    $penjualan = Penjualan::with([
        'pelanggan',
        'details.product'
    ])->findOrFail($penjualanID);

    return response()->json([
        'success' => true,
        'data' => [
            'penjualanID' => $penjualan->penjualanID,
            'kodePesanan' => $penjualan->kodePesanan,
            'namaPelanggan' => $penjualan->pelanggan->namaPelanggan,
            'alamat' => $penjualan->pelanggan->alamat,
            'noTelp' => $penjualan->pelanggan->noTelpPelanggan,
            'waktu' => $penjualan->created_at->format('d/m/Y H:i:s'),
            'totalHarga' => $penjualan->totalHarga,
            'status' => $penjualan->status,
            'diskon' => $penjualan->diskon,
            'uangBayar' => $penjualan->uangBayar,
            'kembalian' => $penjualan->kembalian,
            'items' => $penjualan->details->map(function ($detail) {
                return [
                    'namaProduk' => $detail->product->namaProduk,
                    'quantity' => $detail->jumlahProduk,
                    'harga' => $detail->product->harga,
                    'subtotal' => $detail->subTotal,
                ];
            })
        ]
    ]);
}

    // Proses pembayaran
    public function bayar(Request $request, $penjualanID)
    {
        $request->validate([
            'diskon' => 'nullable|numeric|min:0',
            'uangBayar' => 'required|numeric|min:0',
        ]);

        $penjualan = Penjualan::findOrFail($penjualanID);

        // Hitung total setelah diskon
        $diskon = $request->diskon ?? 0;
        $totalSetelahDiskon = max(0, $penjualan->totalHarga - $diskon);
        $uangBayar = $request->uangBayar;
        $kembalian = max(0, $uangBayar - $totalSetelahDiskon);

        // Pastikan uang cukup
        if ($uangBayar < $totalSetelahDiskon) {
            return response()->json([
                'success' => false,
                'error' => 'Uang bayar tidak cukup!'
            ], 422);
        }

        // Update penjualan
        $penjualan->update([
            'diskon' => $diskon,
            'uangBayar' => $uangBayar,
            'kembalian' => $kembalian,
            'status' => 'paid',
        ]);

        return response()->json([
    'success' => true,
    'message' => 'Pembayaran berhasil!',
    'data' => [
        'kodePesanan' => $penjualan->kodePesanan,
        'namaPelanggan' => $penjualan->pelanggan->namaPelanggan,
        'totalHarga' => $penjualan->totalHarga,
        'diskon' => $penjualan->diskon,
        'uangBayar' => $penjualan->uangBayar,
        'kembalian' => $penjualan->kembalian,
        'items' => $penjualan->details->map(function ($detail) {
            return [
                'namaProduk' => $detail->product->namaProduk,
                'quantity' => $detail->jumlahProduk,
                'harga' => $detail->product->harga,
            ];
        })
    ],
    'redirect' => route('kasir.listPesanan'),
]);
    }

    public function cancel(Request $request, $penjualanID)
{
    $penjualan = Penjualan::findOrFail($penjualanID);

    if ($penjualan->status !== 'pending') {
        return response()->json([
            'success' => false,
            'error' => 'Pesanan sudah dibayar atau dibatalkan.'
        ], 422);
    }

    // Kembalikan stock
    foreach ($penjualan->details as $detail) {
        Product::where('produkID', $detail->produkID)
            ->increment('stock', $detail->jumlahProduk);
    }

    // Update status
    $penjualan->update(['status' => 'cancelled']);

    return response()->json(['success' => true]);
}
}