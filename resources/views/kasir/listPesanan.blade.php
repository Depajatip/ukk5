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
                    <h4>{{ $stats['totalTransaksi'] }}</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 text-center"
                    style="height: 150px; align-items: center; justify-content: center;">
                    <h3>Total Pending</h3>
                    <h4>{{ $stats['pending'] }}</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 text-center"
                    style="height: 150px; align-items: center; justify-content: center;">
                    <h3>Total Cancelled</h3>
                    <h4>{{ $stats['cancelled'] }}</h4>
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
    <div class="btn-group" role="group">
        <button class="btn btn-sm btn-info payment-btn"
                data-id="{{ $penjualan->penjualanID }}"
                data-kode="{{ $penjualan->kodePesanan }}">
            <i class="fas fa-wallet"></i> Payment
        </button>
        <button class="btn btn-sm btn-danger cancel-btn"
                data-id="{{ $penjualan->penjualanID }}"
                data-kode="{{ $penjualan->kodePesanan }}">
            <i class="fas fa-times"></i> Cancel
        </button>
    </div>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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
                        <p><strong>Total:</strong> <span id="totalAsli">Rp 0</span></p>
                        <p><strong>Diskon:</strong> <span id="diskonTampil" class="text-danger">Rp 0</span></p>
                        <h5 class="mb-2">Total Bayar: <span id="totalBayar" class="text-primary fw-bold">Rp 0</span></h5>
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

    <!-- Modal Struk -->
    <div class="modal fade" id="strukModal" tabindex="-1">
        <div class="modal-dialog" style="max-width: 320px;">
            <div class="modal-content" style="font-family: 'Courier New', monospace; font-size: 12px;">
                <div class="modal-header p-2 text-center" style="background:#343a40; color:white;">
                    <div class="w-100">
                        <h6 class="mb-0">STRUK PEMBAYARAN</h6>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-3" id="struk-content">
                    <!-- Diisi via JS -->
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-sm btn-secondary w-100" onclick="printStruk()">
                        <i class="fas fa-print me-1"></i> Cetak Struk
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #strukModal,
            #strukModal * {
                visibility: visible;
            }

            #strukModal {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .modal-dialog {
                max-width: 100% !important;
                margin: 0;
            }

            .modal-content {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
    @push('scripts')
    <script>
        // Fungsi generate struk
        function generateStruk(data) {
            const date = new Date().toLocaleString('id-ID');
            const items = data.items.map(item =>
                `${item.namaProduk.padEnd(20, ' ')}${item.quantity.toString().padStart(3, ' ')} x ${parseInt(item.harga).toLocaleString().padStart(8, ' ')}`
            ).join('\n');

            const diskon = data.diskon || 0;
            const totalSetelahDiskon = data.totalHarga - diskon;
            const kembalian = data.kembalian || 0;

            const struk = `
${'='.repeat(32)}
        WARUNG UKK 5
    Jl. Mangunan No. 123
       Telp: 0812-3456-7890
${'='.repeat(32)}
Kode   : ${data.kodePesanan}
Pelanggan: ${data.namaPelanggan}
Waktu  : ${date}
${'-'.repeat(32)}
${items}
${'-'.repeat(32)}
Total  : Rp ${parseInt(data.totalHarga).toLocaleString()}
Diskon : Rp ${parseInt(diskon).toLocaleString()}
${'>'.repeat(32)}
Bayar  : Rp ${parseInt(data.uangBayar).toLocaleString()}
Kembali: Rp ${parseInt(kembalian).toLocaleString()}
${'='.repeat(32)}
   TERIMA KASIH 🙏
${'='.repeat(32)}
    `.trim();

            // Tampilkan di modal (ganti \n jadi <br>)
            $('#struk-content').html(
                struk.replace(/\n/g, '<br>')
                .replace(/ /g, '&nbsp;')
            );
        }

        // Fungsi cetak
        function printStruk() {
            window.print();
        }
        $(document).ready(function() {
            $('#users-table').DataTable({

            });

            $('.payment-btn').on('click', function() {

                $('#paymentModal').modal('show');
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

            $('#hitungKembalian').on('click', function() {
                const diskon = parseFloat($('#diskon').val()) || 0;
                const uangBayar = parseFloat($('#uangBayar').val()) || 0;
                const totalHarga = parseFloat($('#totalHarga').text().replace(/[^0-9]/g, ''));

                // ✅ Hitung ulang
                const totalSetelahDiskon = Math.max(0, totalHarga - diskon);
                const kembalian = Math.max(0, uangBayar - totalSetelahDiskon);

                // ✅ Tampilkan SEMUA info
                $('#totalAsli').text('Rp ' + totalHarga.toLocaleString());
                $('#diskonTampil').text('Rp ' + diskon.toLocaleString());
                $('#totalBayar').text('Rp ' + totalSetelahDiskon.toLocaleString());
                $('#kembalian').text('Rp ' + kembalian.toLocaleString());

                // ✅ Aktifkan tombol hanya jika uang ≥ total bayar
                $('#paymentForm button[type="submit"]').prop('disabled', uangBayar < totalSetelahDiskon);
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
                        Swal.close();
                        if (res.success) {
                            // ✅ Generate struk
                            generateStruk(res.data);

                            // ✅ Tampilkan modal struk
                            const strukModal = new bootstrap.Modal(document.getElementById('strukModal'));
                            strukModal.show();

                            // ✅ Auto-hide payment modal
                            $('#paymentModal').modal('hide');

                            // ✅ Refresh list setelah struk ditutup
                            $('#strukModal').one('hidden.bs.modal', function() {
                                location.reload();
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
            });
        });

        // Cancel pesanan
$(document).on('click', '.cancel-btn', function() {
    const id = $(this).data('id');
    const kode = $(this).data('kode');

    Swal.fire({
        title: 'Batalkan Pesanan?',
        html: `Kode: <strong>${kode}</strong><br>Yakin ingin membatalkan?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Batalkan',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Memproses...',
                html: 'Membatalkan pesanan',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.post(`/kasir/pesanan/${id}/cancel`, {
                _token: $('meta[name="csrf-token"]').attr('content')
            })
            .done(function(res) {
                Swal.close();
                if (res.success) {
                    Swal.fire('Dibatalkan!', 'Pesanan berhasil dibatalkan.', 'success');
                    location.reload(); // refresh statistik & tabel
                } else {
                    Swal.fire('Gagal!', res.error, 'error');
                }
            })
            .fail(function() {
                Swal.close();
                Swal.fire('Error!', 'Gagal membatalkan pesanan.', 'error');
            });
        }
    });
});
    </script>

    @endpush

</section>
@endsection