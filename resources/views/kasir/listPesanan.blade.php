@extends('layouts.kasirlayout')

@section('title', 'listPesanan')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">list Pesanan</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Kolom 2: Card Statistik -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card-section d-flex g-3">
                    <div class="col-md-4">
                        <div class="card p-3 text-center"
                            style="height: 150px; align-items: center; justify-content: center;">
                            <h3>Total Transaksi</h3>
                            <h4>dummy -</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card p-3 text-center"
                            style="height: 150px; align-items: center; justify-content: center;">
                            <h3>Total Panding</h3>
                            <h4>dummy -</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card p-3 text-center"
                            style="height: 150px; align-items: center; justify-content: center;">
                            <h3>Total gagal</h3>
                            <h4>dummy -</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom 3: DataTable -->
        <div class="row">
            <div class="col-12">
                <div class="table-section">
                    <table id="users-table" class="table table-striped text-center" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th>Nama Pelanggan</th>
                                <th>Kode Pesanan</th>
                                <th>Waktu Pemesanan</th>
                                <th>Total Harga</th>
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
                                <td>{{ $penjualan->created_at->format('H:i:s') }}</td>
                                <td>Rp {{ number_format($penjualan->totalHarga, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge bg-success">Pending</span>
                                    {{-- Nanti: <span class="badge bg-warning">Pending</span> --}}
                                </td>
                                <td>
                                    <button 
                                        class="btn btn-sm btn-info payment-btn"
                                        data-id="{{ $penjualan->penjualanID }}"
                                        data-kode="{{ $penjualan->kodePesanan }}">
                                        <i class="fas fa-wallet"></i> Payment
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada pesanan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Payment -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <!-- Data Pelanggan -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6>Data Pelanggan</h6>
                        <p><strong>Nama:</strong> <span id="namaPelanggan">-</span></p>
                        <p><strong>Alamat:</strong> <span id="alamatPelanggan">-</span></p>
                        <p><strong>No Telp:</strong> <span id="noTelpPelanggan">-</span></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Informasi Pesanan</h6>
                        <p><strong>Kode:</strong> <span id="kodePesanan" class="badge bg-primary">-</span></p>
                        <p><strong>Waktu:</strong> <span id="waktuPesanan">-</span></p>
                        <p><strong>Total:</strong> <span id="totalHarga" class="fw-bold">Rp 0</span></p>
                    </div>
                </div>

                <hr>

                <!-- Daftar Produk -->
                <h6>Daftar Produk</h6>
                <div class="table-responsive">
                    <table class="table table-bordered" id="itemsTable">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Diisi via JS -->
                        </tbody>
                    </table>
                </div>

                <hr>

                <!-- Input Pembayaran -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Diskon (Rp)</label>
                            <input type="number" id="diskon" class="form-control" placeholder="0">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Uang Bayar (Rp)</label>
                            <input type="number" id="uangBayar" class="form-control" placeholder="0" min="0">
                        </div>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="button" id="hitungKembalian" class="btn btn-primary">
                        <i class="fas fa-calculator me-2"></i>Hitung Kembalian
                    </button>
                </div>

                <div class="mt-3 p-3 bg-light rounded">
                    <h6>Kembalian: <span id="kembalian" class="text-success fw-bold">Rp 0</span></h6>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Batal
                </button>
                <form id="paymentForm" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success" disabled>
                        <i class="fas fa-check me-1"></i> Bayar Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#users-table').DataTable({

            });

            $('.payment-btn').on('click', function() {

                $('#paymentMdal').modal('show');
            });

            let currentPenjualanID = null;

    // Buka modal payment
    $(document).on('click', '.payment-btn', function() {
        const id = $(this).data('id');
        currentPenjualanID = id;

        // Reset form
        $('#diskon').val('');
        $('#uangBayar').val('');
        $('#kembalian').text('-');
        $('#paymentForm button[type="submit"]').prop('disabled', true);
        $('#paymentModalLabel').text('Payment: ' + $(this).data('kode'));

        // Ambil data pesanan
        $.get(`/kasir/pesanan/${id}`, function(res) {
            if (res.success) {
                const data = res.data;

                // Isi data pelanggan
                $('#namaPelanggan').text(data.namaPelanggan);
                $('#alamatPelanggan').text(data.alamat);
                $('#noTelpPelanggan').text(data.noTelp);
                $('#kodePesanan').text(data.kodePesanan);
                $('#waktuPesanan').text(data.waktu);
                $('#totalHarga').text('Rp ' + parseInt(data.totalHarga).toLocaleString());

                // Isi daftar produk
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
                $('#itemsTable tbody').html(itemsHtml);

                // Tampilkan modal
                $('#paymentModal').modal('show');
            }
        });
    });

    // Hitung kembalian
    $('#hitungKembalian').on('click', function() {
        const diskon = parseFloat($('#diskon').val()) || 0;
        const uangBayar = parseFloat($('#uangBayar').val()) || 0;
        const totalHarga = parseFloat($('#totalHarga').text().replace(/[^0-9]/g, ''));

        const totalSetelahDiskon = Math.max(0, totalHarga - diskon);
        const kembalian = Math.max(0, uangBayar - totalSetelahDiskon);

        $('#kembalian').text('Rp ' + kembalian.toLocaleString());
        
        // Aktifkan tombol simpan jika uang cukup
        if (uangBayar >= totalSetelahDiskon && uangBayar > 0) {
            $('#paymentForm button[type="submit"]').prop('disabled', false);
        } else {
            $('#paymentForm button[type="submit"]').prop('disabled', true);
        }
    });

    $('#paymentForm').on('submit', function(e) {
    e.preventDefault();

    const diskon = $('#diskon').val() || 0;
    const uangBayar = $('#uangBayar').val();

    // ✅ Loading state
    Swal.fire({
        title: 'Memproses...',
        html: 'Tunggu sebentar',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.post(`/kasir/pesanan/${currentPenjualanID}/bayar`, {
        _token: $('meta[name="csrf-token"]').attr('content'),
        diskon: diskon,
        uangBayar: uangBayar
    })
    .done(function(res) {
        if (res.success) {
            // ✅ Sukses: tampilkan notifikasi dengan kembalian
            Swal.fire({
                icon: 'success',
                title: 'Pembayaran Berhasil!',
                html: `
                    <div class="text-center">
                        <p><strong>Kode Pesanan:</strong> ${$('#kodePesanan').text()}</p>
                    </div>
                `,
                confirmButtonText: 'OK',
                timer: 4000,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.hideLoading();
                }
            }).then(() => {
                $('#paymentModal').modal('hide');
                location.reload(); // refresh list pesanan
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: res.error || 'Terjadi kesalahan saat memproses pembayaran.',
                confirmButtonText: 'Coba Lagi'
            });
        }
    })
    .fail(function(xhr) {
        let errorMsg = 'Gagal memproses pembayaran.';
        if (xhr.status === 422 && xhr.responseJSON?.error) {
            errorMsg = xhr.responseJSON.error;
        } else if (xhr.status === 419) {
            errorMsg = 'Sesi habis. Silakan login ulang.';
        }

        Swal.fire({
            icon: 'error',
            title: 'Error!',
            html: `<pre>${errorMsg}</pre>`,
            confirmButtonText: 'Tutup'
        });
    });
});
        });
    </script>

    @endpush

</section>
@endsection