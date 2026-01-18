<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HistoryPesananController extends Controller
{
    public function index()
    {
        $totalOrders = Penjualan::count();
        $totalCancled = Penjualan::where('status', 'cancelled')->count();
        $totalSuccess = Penjualan::where('status', 'paid')->count();

        $penjualans = Penjualan::with('pelanggan')
        ->whereIn('status', ['paid', 'cancelled'])
        ->latest('created_at')
        ->get();
        // $transaksi = Product::all();
        return view('admin.historyPesanan' , compact('penjualans', 'totalOrders', 'totalCancled', 'totalSuccess'));
    }
}
