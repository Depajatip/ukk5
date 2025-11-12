<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelanggan;

class DataPelangganController extends Controller
{
    public function index()
    {
        return view('admin.dataPelanggan');
    }

    public function dataPelanggan()
    {
        $pelanggans = Pelanggan::all();
        return view('admin.dataPelanggan', compact('pelanggans'));
    }
}
