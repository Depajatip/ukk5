@extends('layouts.kasirlayout')

@section('title', 'orderMenu Kasir')

@section('page-title', 'Selamat Datang, Kasir!')

@section('content')
<div class="container-fluid p-0">
    <div class="row no-gutters">

        <!-- Kolom Kiri: Menu Category + Choose Order -->
        <div class="col-lg-8 col-md-12 bg-light p-3 pb-5" style="height: calc(100vh - 60px); overflow-y: auto;">
            <h2 class="mb-3">Menu Category</h2>

            <!-- Kategori -->
            <div class="d-flex flex-wrap mb-4">
                <!-- Kartu "All" -->
                <div class="category-card d-flex flex-column align-items-center justify-content-center p-3 rounded text-center"
                    style="width: 100px; height: 100px; border: 2px solid #28a745; background-color: #e6f4ea; cursor: pointer;"
                    data-category="all">
                    <i class="fas fa-th-large fa-2x mb-2 text-success"></i>
                    <span><strong>All</strong></span>
                </div>

                @foreach($categories as $category)
                    <?php
                        $catName = strtolower(trim($category->category));
                    ?>
                    <div class="category-card d-flex flex-column align-items-center justify-content-center p-3 rounded text-center"
                        style="width: 100px; height: 100px; border: 2px solid #ddd; cursor: pointer;"
                        data-category="{{ $category->category }}">
                        <div class="bg-light d-flex justify-content-center align-items-center rounded-circle mb-2"
                            style="width: 50px; height: 50px; overflow: hidden;">
                            @if(file_exists(public_path("images/categories/{$catName}.png")))
                                <img src="{{ asset("images/categories/{$catName}.png") }}" alt="{{ $category->category }}"
                                    class="img-fluid" style="object-fit: cover; width: 100%; height: 100%;">
                            @else
                                <i class="fas fa-utensils text-muted"></i>
                            @endif
                        </div>
                        <span>{{ $category->category }}</span>
                    </div>
                @endforeach
            </div>

            <!-- Search Menu -->
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="search-menu" class="form-control" placeholder="Search menu...">
                </div>
            </div>

            <h2 class="mb-3">Choose Order</h2>

            <!-- Daftar Menu -->
            <div class="row" id="menu-list">
                @foreach($products as $product)
                <div class="col-md-3 col-sm-6 product-item mb-3" data-category="{{ $product->category }}" data-name="{{ $product->namaProduk }}" data-id="{{ $product->produkID }}">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="bg-secondary d-flex justify-content-center align-items-center"
                                style="height: 150px; border-radius: 8px;">
                                @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->namaProduk }}"
                                    class="img-fluid rounded" style="max-height: 100%; max-width: 100%;">
                                @else
                                <span class="text-white">No Image</span>
                                @endif
                            </div>
                            <h5 class="mt-3">{{ $product->namaProduk }}</h5>
                            <p class="text-muted mb-1">Rp {{ number_format($product->harga, 0, ',', '.') }}</p>

                            {{-- ✅ Tampilan Stock --}}
                            <p class="mb-3">
                                @if($product->stock > 0)
                                <span class="badge stock-badge {{ $product->stock > 0 ? 'bg-success' : 'bg-danger' }}"
                                    data-product-id="{{ $product->produkID }}"> {{-- opsional tapi bagus --}}
                                    @if($product->stock > 0)
                                    <i class="fas fa-box me-1"></i> Stock: {{ $product->stock }}
                                    @else
                                    <i class="fas fa-times me-1"></i> Habis
                                    @endif
                                </span>
                                @endif
                            </p>

                            <button class="btn btn-warning btn-sm mt-auto add-to-cart"
                                data-id="{{ $product->produkID }}"
                                data-name="{{ $product->namaProduk }}"
                                data-price="{{ $product->harga }}"
                                {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                Masukan Keranjang <i class="fas fa-shopping-cart ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Kolom Kanan: Order Menu -->
        <div class="col-lg-4 col-md-12 bg-success-subtle p-3 pb-5" style="height: calc(100vh - 60px); display: flex; flex-direction: column;">
            <div class="d-flex align-items-center mb-3">
                <i class="fas fa-receipt fa-2x me-2 text-success"></i>
                <div>
                    <h3 class="m-0">Order Menu</h3>
                    <small>Order No.16</small>
                </div>
            </div>

            <hr>

            <!-- Daftar Pesanan: BISA SCROLL -->
            <div id="order-list" class="mb-3 flex-grow-1" style="overflow-y: auto; max-height: calc(100% - 200px);">
                <div class="alert alert-info text-center">Belum ada pesanan.</div>
            </div>

            <hr>

            <!-- Form Input Pelanggan: SELALU DI BAWAH -->
            <div style="margin-top: auto;">
                <div class="mb-3">
                    <input type="text" id="customer-name" class="form-control" placeholder="Nama Pelanggan..." />
                </div>
                <div class="row">
                    <div class="col-6">
                        <input type="text" id="customer-phone" class="form-control" placeholder="No Telp..." />
                    </div>
                    <div class="col-6">
                        <input type="text" id="customer-address" class="form-control" placeholder="Alamat..." />
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center p-3 bg-orange rounded"
                    style="background-color: #FF7A00; color: white;">
                    <div>
                        <span id="total-items">0</span> Items<br>
                        <strong>Rp <span id="total-price">0</span></strong>
                    </div>
                    <button id="place-order" class="btn btn-light rounded-pill px-4">Order</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let cart = [];

    // ✅ Update UI stock berdasarkan produkID & newStock
    function updateProductStockUI(produkID, newStock) {
        const $card = $(`.product-item[data-id="${produkID}"]`);
        if ($card.length === 0) {
            console.warn(`Product card with id=${produkID} not found!`);
            return;
        }

        const $badge = $card.find('.stock-badge');
        const $btn = $card.find('.add-to-cart');

        if (newStock > 0) {
            $badge.removeClass('bg-danger').addClass('bg-success')
                .html(`<i class="fas fa-box me-1"></i> Stock: ${newStock}`);
            $btn.prop('disabled', false);
        } else {
            $badge.removeClass('bg-success').addClass('bg-danger')
                .html(`<i class="fas fa-times me-1"></i> Habis`);
            $btn.prop('disabled', true);
        }
    }

    // ✅ Event delegation untuk +/- (aman, hanya sekali)
    $(document).on('click', '.plus-btn, .minus-btn', function() {
        const id = String($(this).data('id'));
        const isPlus = $(this).hasClass('plus-btn');
        const itemIndex = cart.findIndex(it => it.id === id);

        if (itemIndex === -1) return;

        if (isPlus) {
            // Tambah qty → kurangi stock
            cart[itemIndex].quantity += 1;
            const $badge = $(`.product-item[data-id="${id}"] .stock-badge`);
            const currentStock = parseInt($badge.text().match(/\d+/)?.[0] || 0);
            updateProductStockUI(id, currentStock - 1);
        } else {
            // Kurangi qty
            if (cart[itemIndex].quantity > 1) {
                cart[itemIndex].quantity -= 1;
                const $badge = $(`.product-item[data-id="${id}"] .stock-badge`);
                const currentStock = parseInt($badge.text().match(/\d+/)?.[0] || 0);
                updateProductStockUI(id, currentStock + 1);
            } else {
                // Hapus item → restore stock ke nilai awal
                const initialStock = $(`.product-item[data-id="${id}"]`).data('initial-stock') || 0;
                updateProductStockUI(id, initialStock);
                cart.splice(itemIndex, 1);
            }
        }

        updateCart();
    });

    // ✅ Event untuk "Masukan Keranjang"
    $(document).on('click', '.add-to-cart', function() {
        const id = String($(this).data('id'));
        const name = $(this).data('name');
        const price = parseFloat($(this).data('price'));
        const image = $(this).closest('.product-item').find('img').attr('src') || null;

        const $card = $(`.product-item[data-id="${id}"]`);
        const $badge = $card.find('.stock-badge');
        const currentStock = parseInt($badge.text().match(/\d+/)?.[0] || 0);

        if (currentStock <= 0) {
            alert('Stok habis!');
            return;
        }

        // ✅ Kurangi stock di UI
        updateProductStockUI(id, currentStock - 1);

        // Tambah ke keranjang
        const existing = cart.find(item => item.id === id);
        if (existing) {
            existing.quantity += 1;
        } else {
            cart.push({
                id,
                name,
                price,
                quantity: 1,
                image: image
            });
        }

        updateCart();
    });

    // ✅ Render keranjang (tanpa event dalam loop!)
    function updateCart() {
    const $orderList = $('#order-list');
    const $totalItems = $('#total-items');
    const $totalPrice = $('#total-price');

    if (cart.length === 0) {
        $orderList.html('<div class="alert alert-info text-center">Belum ada pesanan.</div>');
        $totalItems.text('0');
        $totalPrice.text('0');
        return;
    }

    let html = '';
    let total = 0;
    let itemCount = 0;

    cart.forEach(item => {
        // ✅ Ambil current stock DARI DOM (per item, di dalam loop)
        const $stockBadge = $(`.product-item[data-id="${item.id}"] .stock-badge`);
        const currentStockText = $stockBadge.text();
        const currentStock = parseInt(currentStockText.match(/\d+/)?.[0] || 0);
        const plusDisabled = currentStock <= 0 ? 'disabled' : '';

        const subtotal = item.price * item.quantity;
        total += subtotal;
        itemCount += item.quantity;

html += `
<div class="order-item p-3 mb-2 bg-white rounded shadow-sm"
     style="position: relative; min-height: 76px; padding-right: 40px;">
<button class="btn btn-sm remove-btn"
        style="position: absolute; top: 2px; right: 0px; width: 24px; height: 24px; padding: 0; font-size: 0.7rem; z-index: 5;"
        data-id="${item.id}" title="Hapus">
    <i class="fas fa-times"></i>
</button>

    <!-- Konten: Gambar + Nama + Harga + +/- -->
    <div class="d-flex align-items-center">
        <div class="mr-3 flex-shrink-0" style="width: 50px; height: 50px; border-radius: 8px; overflow: hidden;">
    ${item.image 
        ? `<img src="${item.image}" alt="${item.name}" class="w-100 h-100" style="object-fit: cover;">`
        : `<div class="bg-secondary w-100 h-100 d-flex align-items-center justify-content-center">
             <span class="text-white small">?</span>
           </div>`
    }
</div>

        <!-- Nama & Harga -->
        <div class="flex-grow-1 mr-3" style="min-width: 0;">
            <div class="font-weight-bold text-truncate">${item.name}</div>
            <div class="text-muted small">Rp ${item.price.toLocaleString()}</div>
        </div>

        <!-- Tombol +/- -->
        <div class="d-flex align-items-center" style="flex-shrink: 0;">
            <button class="btn btn-sm btn-outline-danger minus-btn" data-id="${item.id}" 
                    style="width: 28px; height: 28px; padding: 0; font-size: 0.85rem;">−</button>
            <span class="mx-2 font-weight-bold">${item.quantity}</span>
            <button class="btn btn-sm btn-outline-success plus-btn" data-id="${item.id}" ${plusDisabled}
                    style="width: 28px; height: 28px; padding: 0; font-size: 0.85rem;">+</button>
        </div>
    </div>
</div>`;
    });

    $orderList.html(html);
    $totalItems.text(itemCount);
    $totalPrice.text(total.toLocaleString());
}
$(document).on('click', '.remove-btn', function(e) {
    e.preventDefault();
    e.stopPropagation();

    const id = String($(this).data('id'));
    const itemIndex = cart.findIndex(it => it.id === id);

    if (itemIndex === -1) return;

    const item = cart[itemIndex];
    const qty = item.quantity;

    // ✅ Pulihkan stock
    const $badge = $(`.product-item[data-id="${id}"] .stock-badge`);
    const currentStock = parseInt($badge.text().match(/\d+/)?.[0] || 0);
    updateProductStockUI(id, currentStock + qty);

    // Hapus dari keranjang
    cart.splice(itemIndex, 1);

    // Refresh tampilan
    updateCart();
});

    // ✅ Simpan initial stock saat halaman load
    $(document).ready(function() {
        $('.product-item').each(function() {
            const $badge = $(this).find('.stock-badge');
            const stockMatch = $badge.text().match(/Stock:\s*(\d+)/);
            const initialStock = stockMatch ? parseInt(stockMatch[1]) : 0;
            $(this).data('initial-stock', initialStock);
        });

        // Inisialisasi tampilan keranjang
        updateCart();
    });

    // ✅ Filter & search (sama)
    document.querySelectorAll('.category-card').forEach(card => {
        card.addEventListener('click', function () {
            const selectedCategory = this.dataset.category;

            document.querySelectorAll('.product-item').forEach(item => {
                if (selectedCategory === 'all') {
                    item.style.display = 'block'; // Tampilkan semua
                } else {
                    // Bandingkan dengan kategori produk
                    item.style.display = (item.dataset.category === selectedCategory) ? 'block' : 'none';
                }
            });

            // Highlight kategori yang dipilih
            document.querySelectorAll('.category-card').forEach(c => {
                c.style.borderColor = '#ddd';
                c.style.backgroundColor = '';
            });
            this.style.borderColor = '#28a745';
            this.style.backgroundColor = '#e6f4ea';
        });
    });

    // Search menu
    document.getElementById('search-menu').addEventListener('input', function () {
        const query = this.value.toLowerCase();
        document.querySelectorAll('.product-item').forEach(item => {
            const name = item.dataset.name.toLowerCase();
            item.style.display = name.includes(query) ? 'block' : 'none';
        });
    });
$('#place-order').on('click', function() {
    if (cart.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Keranjang Kosong!',
            text: 'Tambahkan produk terlebih dahulu.',
            confirmButtonText: 'OK'
        });
        return;
    }

    const name = $('#customer-name').val().trim();
    const phone = $('#customer-phone').val().trim();
    const addr = $('#customer-address').val().trim();

    if (!name || !phone || !addr) {
        Swal.fire({
            icon: 'warning',
            title: 'Data Belum Lengkap!',
            text: 'Silakan isi nama, no telp, dan alamat pelanggan.',
            confirmButtonText: 'OK'
        });
        return;
    }

    // ✅ Validasi stock akhir
    let stockIssues = [];
    cart.forEach(item => {
        const $badge = $(`.product-item[data-id="${item.id}"] .stock-badge`);
        const currentStock = parseInt($badge.text().match(/\d+/)?.[0] || 0);
        if (currentStock < 0) {
            const productName = $(`.product-item[data-id="${item.id}"]`).data('name');
            stockIssues.push(productName);
        }
    });

    if (stockIssues.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Stok Tidak Mencukupi!',
            html: `Produk habis: <strong>${stockIssues.join(', ')}</strong>`,
            confirmButtonText: 'Perbaiki Keranjang'
        });
        return;
    }

    // ✅ Loading state
    Swal.fire({
        title: 'Memproses...',
        html: 'Menyimpan pesanan',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    $.ajax({
        url: "{{ route('kasir.orderMenu.store') }}",
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            namaPelanggan: name,
            alamat: addr,
            noTelpPelanggan: phone,
            cart: cart.map(item => ({
                id: item.id,
                quantity: item.quantity
            }))
        },
        success: function(res) {
            if (res.success) {
                // ✅ Hanya tampilkan centang + "Order Success"
                Swal.fire({
                    icon: 'success',
                    title: 'Order Success!',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.hideLoading();
                    }
                }).then(() => {
                    // Reset keranjang & form
                    cart = [];
                    updateCart();
                    $('#customer-name, #customer-phone, #customer-address').val('');
                    
                    // Redirect ke list pesanan
                    window.location.href = res.redirect;
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: res.error || 'Gagal menyimpan order.',
                    confirmButtonText: 'OK'
                });
            }
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                html: `<pre>${xhr.responseJSON?.error || 'Terjadi kesalahan. Coba lagi.'}</pre>`,
                confirmButtonText: 'Tutup'
            });
        }
    });
});
</script>
@endpush
<!-- Modal Sukses -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-custom">
                <div class="modal-content text-center p-4" style="height: 350px;">
                    <div class="modal-body">
                        <!-- Ikon centang besar -->
                        <div class="text-success mb-3">
                            <i class="fas fa-check-circle fa-10x"></i>
                        </div>
                        <br>
                        <!-- Teks sukses -->
                        <h3 class="fw-bold">Aksi Berhasil Dilakukan</h3>
                    </div>
                </div>
            </div>
        </div>
@endsection