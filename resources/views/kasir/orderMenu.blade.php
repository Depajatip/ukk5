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
                    <div class="col-md-3 col-sm-6 product-item mb-3" data-category="{{ $product->category }}"
                        data-name="{{ $product->namaProduk }}">
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
                                <p class="text-muted mb-3">Rp {{ number_format($product->harga, 0, ',', '.') }}</p>
                                <button class="btn btn-warning btn-sm mt-auto add-to-cart" data-id="{{ $product->produkID }}"
                                                data-name="{{ $product->namaProduk }}"
                                                data-price="{{ $product->harga }}">
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
    // Data keranjang
    let cart = [];

    // Fungsi tambah ke keranjang
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function () {
            const id = String(this.dataset.id);
            const name = this.dataset.name;
            const price = parseFloat(this.dataset.price);

            const existing = cart.find(item => item.id === String(id));
            if (existing) {
                existing.quantity += 1;
            } else {
                cart.push({
                    id: String(id),
                    name: name,
                    price: price,
                    quantity: 1
                });
            }

            updateCart();
        });
    });

    // Fungsi update tampilan keranjang
    function updateCart() {
        const orderList = document.getElementById('order-list');
        const totalItems = document.getElementById('total-items');
        const totalPrice = document.getElementById('total-price');

        if (cart.length === 0) {
            orderList.innerHTML = '<div class="alert alert-info text-center">Belum ada pesanan.</div>';
            totalItems.textContent = '0';
            totalPrice.textContent = '0';
            return;
        }

        let html = '';
        let total = 0;
        let itemCount = 0;

        cart.forEach(item => {
            const subtotal = item.price * item.quantity;
            total += subtotal;
            itemCount += item.quantity;

            html += `
            <div class="order-item d-flex align-items-center justify-content-between p-3 mb-2 bg-white rounded shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="bg-secondary d-flex justify-content-center align-items-center" style="width: 50px; height: 50px; border-radius: 8px; margin-right: 10px;">
                        <i class="fas fa-hamburger text-white"></i>
                    </div>
                    <div>
                        <div>${item.name}</div>
                        <div class="text-muted">Rp ${item.price.toLocaleString()}</div>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <button class="btn btn-sm btn-outline-danger minus-btn" data-id="${item.id}">-</button>
                    <span class="mx-2">${item.quantity}</span>
                    <button class="btn btn-sm btn-outline-success plus-btn" data-id="${item.id}">+</button>
                </div>
            </div>`;
        });

        orderList.innerHTML = html;
        totalItems.textContent = itemCount;
        totalPrice.textContent = total.toLocaleString();

        // Event listener untuk tombol +/- di keranjang
        document.querySelectorAll('.minus-btn').forEach(button => {
            button.addEventListener('click', function () {
                const id = String(this.dataset.id);
                const item = cart.find(item => item.id === id);
                if (item.quantity > 1) {
                    item.quantity -= 1;
                } else {
                    cart = cart.filter(item => item.id !== id);
                }
                updateCart();
            });
        });

        document.querySelectorAll('.plus-btn').forEach(button => {
            button.addEventListener('click', function () {
                const id = String(this.dataset.id);
                const item = cart.find(item => item.id === id);
                item.quantity += 1;
                updateCart();
            });
        });
    }

    // Filter kategori
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

    // Tombol Order
    document.getElementById('place-order').addEventListener('click', function () {
        if (cart.length === 0) {
            alert('Keranjang kosong!');
            return;
        }

        const customerName = document.getElementById('customer-name').value.trim();
        const customerPhone = document.getElementById('customer-phone').value.trim();
        const customerAddress = document.getElementById('customer-address').value.trim();

        if (!customerName || !customerPhone || !customerAddress) {
            alert('Silakan isi semua data pelanggan!');
            return;
        }

        // Simulasi order
        alert(`Order berhasil!\n\nNama: ${customerName}\nNo Telp: ${customerPhone}\nAlamat: ${customerAddress}\nTotal: Rp ${document.getElementById('total-price').textContent}`);

        // Reset
        cart = [];
        updateCart();
        document.getElementById('customer-name').value = '';
        document.getElementById('customer-phone').value = '';
        document.getElementById('customer-address').value = '';
    });
</script>
@endpush
@endsection