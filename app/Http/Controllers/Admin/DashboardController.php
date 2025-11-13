<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use App\Models\Product;
use App\Models\DetailPenjualan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
        public function index()
    {
        return view('admin.dashboard');
    }

    public function data(Request $request)
    {
        $start = $end = now();
        
        if ($request->has('days')) {
            $days = $request->days;
            $start = $days == 0 
                ? now()->startOfDay() 
                : now()->subDays($days)->startOfDay();
        } elseif ($request->has('start') && $request->has('end')) {
            $start = Carbon::parse($request->start)->startOfDay();
            $end = Carbon::parse($request->end)->endOfDay();
        }
$paidOrdersQuery = Penjualan::where('status', 'paid')
    ->whereBetween('created_at', [$start, $end]);
        // Statistik
$paidOrderIds = $paidOrdersQuery->pluck('penjualanID');

$stats = [
    'totalPendapatan' => $paidOrdersQuery->sum('totalHarga'),
    'totalTransaksi' => $paidOrderIds->count(),
    'totalProdukTerjual' => DetailPenjualan::whereIn('penjualanID', $paidOrderIds)
        ->sum('jumlahProduk'),
];

        // Grafik: 7 hari terakhir (default)
        $chartStart = now()->subDays(6)->startOfDay();
        $chartEnd = now()->endOfDay();
        $labels = [];
        $values = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $labels[] = $date->format('d M');
            $values[] = Penjualan::where('status', 'paid')
                ->whereDate('created_at', $date)
                ->sum('totalHarga');
        }

        // Produk terlaris
$topProducts = DetailPenjualan::selectRaw('products.namaProduk, SUM(jumlahProduk) as totalTerjual, SUM(subTotal) as revenue')
    ->join('products', 'detailPenjualans.produkID', '=', 'products.produkID')
    ->whereIn('detailPenjualans.penjualanID', $paidOrderIds)
    ->groupBy('products.produkID', 'products.namaProduk') // ✅ Tambahkan namaProduk di sini!
    ->orderByDesc('totalTerjual')
    ->limit(5)
    ->get();

        // Stok menipis
        $lowStock = Product::where('stock', '<=', 5)
            ->where('stock', '>', 0)
            ->select('namaProduk', 'stock')
            ->get();

        $lowStockCount = Product::where('stock', '<=', 0)->count() + $lowStock->count();

        // Transaksi terbaru
        $recentTransactions = Penjualan::with('pelanggan')
            ->whereIn('status', ['paid', 'pending'])
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function ($t) {
                return [
                    'kodePesanan' => $t->kodePesanan,
                    'namaPelanggan' => $t->pelanggan->namaPelanggan,
                    'waktu' => $t->created_at->format('d/m H:i'),
                    'totalHarga' => $t->totalHarga,
                    'status' => $t->status,
                ];
            });

        return response()->json([
            'stats' => $stats,
            'salesData' => [
                'labels' => $labels,
                'values' => $values,
            ],
            'topProducts' => $topProducts,
            'lowStock' => $lowStock,
            'lowStockCount' => $lowStockCount,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
