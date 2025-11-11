<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;


namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class listPesananController extends Controller
{
    public function index()
{
    $categories = \App\Models\Product::select('category')->distinct()->get();

    $products = \App\Models\Product::all();

    return view('kasir.listPesanan', compact('categories', 'products'));
}
}
