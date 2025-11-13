<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penjualan; // ✅ import model Penjualan

class listPesananController extends Controller
{
    public function index()
    {
        // ✅ Ambil data penjualan + relasi pelanggan, urut terbaru
    $penjualans = Penjualan::with('pelanggan')
        ->where('status', 'pending')
        ->latest('created_at')
        ->get();

        return view('kasir.listPesanan', compact('penjualans'));
    }
}