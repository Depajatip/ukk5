<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransaksiController extends Controller
{
    public function listTransaksi()
    {
        // $transaksi = Product::all();
        return view('admin.listTransaksi');
    }
}
