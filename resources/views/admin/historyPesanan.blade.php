@extends('layouts.adminlte')

@section('title', 'History Pesanan')

@section('content')
<div class="container-fluid p-3">
    <h2>History Pesanan</h2>

            <!-- Kolom 2: Card Statistik -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card-section d-flex g-3">
                        <div class="col-md-4">
                            <div class="card p-3 text-center"
                                style="height: 150px; align-items: center; justify-content: center;">
                                <h3>Total Orders</h3>
                                <h4>{{ $totalOrders }} -</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card p-3 text-center"
                                style="height: 150px; align-items: center; justify-content: center;">
                                <h3>Total Cancled</h3>
                                <h4>{{ $totalCancled }} -</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card p-3 text-center"
                                style="height: 150px; align-items: center; justify-content: center;">
                                <h3>Total Success</h3>
                                <h4>{{ $totalSuccess }} -</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="historyTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Pelanggan</th>
                    <th>Kode Pesanan</th>
                    <th>Waktu</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penjualans as $penjualan)
                <tr>
                    <td>{{ $penjualan->penjualanID }}</td>
                    <td>{{ $penjualan->pelanggan->namaPelanggan ?? '-' }}</td>
                    <td>{{ $penjualan->kodePesanan ?? '-' }}</td>
                    <td>{{ $penjualan->created_at->format('d/m/Y H:i') }}</td>
                    <td>Rp {{ number_format($penjualan->totalHarga, 0, ',', '.') }}</td>
                    <td>
                        @if($penjualan->status === 'paid')
                            <span class="badge bg-success">Paid</span>
                        @elseif($penjualan->status === 'cancelled')
                            <span class="badge bg-danger">Cancelled</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($penjualan->status) }}</span>
                        @endif
                    </td>
                    <td>
                        <button 
                            class="btn btn-sm btn-info detail-btn"
                            data-id="{{ $penjualan->penjualanID }}"
                            data-kode="{{ $penjualan->kodePesanan }}">
                            <i class="fas fa-eye"></i> Detail
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Belum ada riwayat pesanan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Detail (mirip payment, tapi read-only) -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Pesanan</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
            </div>

            <div class="modal-body">
                <!-- Data Pelanggan -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4 class="font-weight-bold">Data Pelanggan</h4>
                        <p><strong>Nama:</strong> <span id="d_namaPelanggan">-</span></p>
                        <p><strong>Alamat:</strong> <span id="d_alamatPelanggan">-</span></p>
                        <p><strong>No Telp:</strong> <span id="d_noTelpPelanggan">-</span></p>
                    </div>
                    <div class="col-md-6">
                        <h4 class="font-weight-bold">Informasi Pesanan</h4>
                        <p><strong>Waktu:</strong> <span id="d_waktuPesanan">-</span></p>
                        <p><strong>Total:</strong> <span id="d_totalHarga" class="fw-bold">Rp 0</span></p>
                        @if($penjualans->first()?->diskon)
                        <p><strong>Diskon:</strong> <span id="d_diskon">Rp 0</span></p>
                        <p><strong>Uang Bayar:</strong> <span id="d_uangBayar">Rp 0</span></p>
                        <p><strong>Kembalian:</strong> <span id="d_kembalian" class="text-success">Rp 0</span></p>
                        @endif
                    </div>
                </div>

                <hr>

                <!-- Daftar Produk -->
                <h6>Daftar Produk</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="d_itemsTable">
                            <!-- Diisi via JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Buka modal detail
    $(document).on('click', '.detail-btn', function() {
        const id = $(this).data('id');

        $.get(`/kasir/pesanan/${id}`, function(res) {
            if (res.success) {
                const data = res.data;

                // Isi data
                $('#d_namaPelanggan').text(data.namaPelanggan);
                $('#d_alamatPelanggan').text(data.alamat);
                $('#d_noTelpPelanggan').text(data.noTelp);
                $('#d_kodePesanan').text(data.kodePesanan);
                $('#d_waktuPesanan').text(data.waktu);
                $('#d_totalHarga').text('Rp ' + parseInt(data.totalHarga).toLocaleString());

                // Status
                const statusBadge = $('#d_status');
                statusBadge.text(data.status === 'paid' ? 'Paid' : 'Cancelled');
                statusBadge.removeClass('bg-secondary bg-success bg-danger');
                statusBadge.addClass(data.status === 'paid' ? 'bg-success' : 'bg-danger');

                // Diskon & pembayaran (jika ada)
                $('#d_diskon').text('Rp ' + parseInt(data.diskon ?? 0).toLocaleString());
                $('#d_uangBayar').text('Rp ' + parseInt(data.uangBayar ?? 0).toLocaleString());
                $('#d_kembalian').text('Rp ' + parseInt(data.kembalian ?? 0).toLocaleString());

                // Items
                let itemsHtml = '';
                data.items.forEach(item => {
                    itemsHtml += `
                    <tr>
                        <td>${item.namaProduk}</td>
                        <td>${item.quantity}</td>
                        <td>Rp ${parseInt(item.harga).toLocaleString()}</td>
                        <td>Rp ${parseInt(item.subtotal).toLocaleString()}</td>
                    </tr>`;
                });
                $('#d_itemsTable').html(itemsHtml);

                // Tampilkan modal
                $('#detailModalLabel').text('Detail: ' + data.kodePesanan);
                $('#detailModal').modal('show');
            }
        });
    });

    // Inisialisasi DataTable (opsional)
    $('#historyTable').DataTable({
        "pageLength": 8,
        "order": [[0, 'desc']]
    });
});
</script>
@endpush
@endsection