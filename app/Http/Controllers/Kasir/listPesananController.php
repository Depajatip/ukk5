<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penjualan; // ✅ import model Penjualan

class listPesananController extends Controller
{
public function index()
{
    // Hanya ambil pesanan pending (yang muncul di tabel)
    $penjualans = Penjualan::with('pelanggan')
        ->where('status', 'pending')
        ->latest('created_at')
        ->get();

    $stats = [
        'totalTransaksi' => $penjualans->count(), // ✅ sesuai jumlah baris di tabel
        'pending' => $penjualans->count(),
        'cancelled' => Penjualan::where('status', 'cancelled')->count(),
    ];

    return view('kasir.listPesanan', compact('penjualans', 'stats'));
}
}