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

        $penjualans = Penjualan::with('pelanggan')
        ->whereIn('status', ['paid', 'cancelled'])
        ->latest('created_at')
        ->get();
        // $transaksi = Product::all();
        return view('admin.historyPesanan' , compact('penjualans'));
    }
}
