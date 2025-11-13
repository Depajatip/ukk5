@extends('layouts.adminlte')

@section('title', 'Dashboard')

@push('styles')
<!-- Chart.js -->
<link href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Dashboard</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <!-- Filter Tanggal -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary date-filter active" data-days="0">Hari Ini</button>
                    <button type="button" class="btn btn-outline-primary date-filter" data-days="7">7 Hari</button>
                    <button type="button" class="btn btn-outline-primary date-filter" data-days="30">30 Hari</button>
                    <button type="button" class="btn btn-outline-primary date-filter" data-days="custom">Custom</button>
                </div>
                <div id="custom-date-range" class="d-none mt-2">
                    <input type="date" id="start-date" class="form-control d-inline w-auto" style="width:150px;">
                    <span class="mx-2">s/d</span>
                    <input type="date" id="end-date" class="form-control d-inline w-auto" style="width:150px;">
                    <button id="apply-date" class="btn btn-sm btn-success ml-2">Terapkan</button>
                </div>
            </div>
        </div>

        <!-- Statistik Cards -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="total-pendapatan">Rp 0</h3>
                        <p>Pendapatan</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="total-transaksi">0</h3>
                        <p>Transaksi</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 id="total-produk-terjual">0</h3>
                        <p>Produk Terjual</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 id="stok-menipis">0</h3>
                        <p>Stok Menipis</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik & Tabel -->
        <div class="row">
            <!-- Grafik Penjualan -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Grafik Penjualan (7 Hari Terakhir)</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Produk Terlaris -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Produk Terlaris</h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="products-list product-list-in-card pl-2 pr-2" id="top-products">
                            <!-- Diisi via JS -->
                        </ul>
                    </div>
                </div>

                <!-- Peringatan Stok -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Stok Menipis <small>(≤5)</small></h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="products-list product-list-in-card pl-2 pr-2" id="low-stock">
                            <!-- Diisi via JS -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaksi Terbaru -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Transaksi Terbaru</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover" id="recent-transactions">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Pelanggan</th>
                                    <th>Waktu</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="transactions-body">
                                <!-- Diisi via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
$(document).ready(function() {
    let currentRange = { days: 7 };

    // Inisialisasi tanggal custom
    $('#start-date').val(new Date().toISOString().split('T')[0]);
    $('#end-date').val(new Date().toISOString().split('T')[0]);

    // Filter tanggal
    $('.date-filter').on('click', function() {
        $('.date-filter').removeClass('active');
        $(this).addClass('active');
        const days = $(this).data('days');

        if (days === 'custom') {
            $('#custom-date-range').removeClass('d-none');
        } else {
            $('#custom-date-range').addClass('d-none');
            currentRange = { days: parseInt(days) };
            loadDashboardData();
        }
    });

    $('#apply-date').on('click', function() {
        const start = $('#start-date').val();
        const end = $('#end-date').val();
        if (start && end) {
            currentRange = { start, end };
            loadDashboardData();
        }
    });

    // Ambil data dashboard
    function loadDashboardData() {
        $.get("{{ route('admin.dashboard.data') }}", currentRange, function(res) {
            // Update statistik
            $('#total-pendapatan').text('Rp ' + res.stats.totalPendapatan.toLocaleString());
            $('#total-transaksi').text(res.stats.totalTransaksi);
            $('#total-produk-terjual').text(res.stats.totalProdukTerjual);
            $('#stok-menipis').text(res.lowStockCount);

            // Grafik penjualan
            renderSalesChart(res.salesData);

            // Produk terlaris
            renderTopProducts(res.topProducts);

            // Stok menipis
            renderLowStock(res.lowStock);

            // Transaksi terbaru
            renderRecentTransactions(res.recentTransactions);
        });
    }

    // Render grafik
    let salesChart = null;
    function renderSalesChart(data) {
        const ctx = document.getElementById('salesChart').getContext('2d');
        if (salesChart) salesChart.destroy();

        salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: data.values,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (val) => 'Rp ' + val.toLocaleString()
                        }
                    }
                }
            }
        });
    }

    // Render produk terlaris
    function renderTopProducts(products) {
        let html = '';
        products.forEach(p => {
            html += `
            <li class="item">
                <div class="product-img">
                    <i class="fas fa-hamburger bg-info"></i>
                </div>
                <div class="product-info">
                    <a href="#" class="product-title">${p.namaProduk}
                        <span class="badge bg-success float-right">Rp ${p.revenue.toLocaleString()}</span>
                    </a>
                    <span class="product-description">Terjual: ${p.totalTerjual} pcs</span>
                </div>
            </li>`;
        });
        $('#top-products').html(html || '<li class="item text-center">Tidak ada data</li>');
    }

    // Render stok menipis
    function renderLowStock(items) {
        let html = '';
        items.forEach(item => {
            const progress = Math.min(100, (item.stock / 5) * 100);
            html += `
            <li class="item">
                <div class="product-img">
                    <i class="fas fa-box bg-warning"></i>
                </div>
                <div class="product-info">
                    <a href="#" class="product-title">${item.namaProduk}
                        <span class="badge ${item.stock <= 0 ? 'bg-danger' : 'bg-warning'} float-right">${item.stock}</span>
                    </a>
                    <div class="progress progress-xs">
                        <div class="progress-bar ${item.stock <= 0 ? 'bg-danger' : 'bg-warning'}" 
                             style="width: ${progress}%"></div>
                    </div>
                    <span class="product-description">Stok tersisa</span>
                </div>
            </li>`;
        });
        $('#low-stock').html(html || '<li class="item text-center">Stok aman</li>');
    }

    // Render transaksi terbaru
    function renderRecentTransactions(transactions) {
        let html = '';
        transactions.forEach(t => {
            const statusBadge = t.status === 'paid' 
                ? '<span class="badge bg-success">Paid</span>'
                : '<span class="badge bg-warning">Pending</span>';
            html += `
            <tr>
                <td>${t.kodePesanan}</td>
                <td>${t.namaPelanggan}</td>
                <td>${t.waktu}</td>
                <td>Rp ${t.totalHarga.toLocaleString()}</td>
                <td>${statusBadge}</td>
            </tr>`;
        });
        $('#transactions-body').html(html || '<tr><td colspan="5" class="text-center">Tidak ada transaksi</td></tr>');
    }

    // Load data awal
    loadDashboardData();
});
</script>
@endpush