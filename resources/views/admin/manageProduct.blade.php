@extends('layouts.adminlte')

@section('title', 'manageProduct')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Manage Product</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Kolom 1: Judul & Tombol -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="header-section">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#addProductModal">
                        <i class="fas fa-plus"></i> Tambah Product
                    </button>
                </div>
            </div>
        </div>

        <!-- Kolom 3: DataTable -->
        <div class="row">
            <div class="col-12">
                <div class="table-section">
                    <table id="products-table" class="table table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Gambar</th>
                                <th class="text-center">Nama</th>
                                <th class="text-center">Category</th>
                                <th class="text-center">Stock</th>
                                <th class="text-center">Harga</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr>
                                <td class="text-center">{{ $product->produkID }}</td>
                                <td class="text-center">
                                    @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" width="50"
                                        class="img-thumbnail">
                                    @else
                                    <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $product->namaProduk }}</td>
                                <td class="text-center">{{ $product->category }}</td>
                                <td class="text-center">{{ $product->stock }}</td>
                                <td class="text-center">Rp {{ number_format($product->harga, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-sm btn-primary edit-btn"
                                        data-produkid="{{ $product->produkID }}"
                                        data-nama-produk="{{ $product->namaProduk }}"
                                        data-category="{{ $product->category }}"
                                        data-harga="{{ $product->harga }}"
                                        data-stock="{{ $product->stock }}"
                                        data-image="{{ $product->image }}"
                                        title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form id="deleteProductForm{{ $product->produkID }}"
                                        action="{{ route('admin.product.destroy', $product->produkID) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" title="Hapus"
                                            onclick="openDeleteModal({{ $product->produkID }})">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#products-table').DataTable({

            });
        });
        $(document).ready(function() {
            $('.edit-btn').on('click', function() {
                const produkID = $(this).data('produkid');
                const namaProduk = $(this).data('nama-produk'); // perhatikan: gunakan nama yg sama persis seperti di HTML
                const category = $(this).data('category');
                const harga = $(this).data('harga');
                const stock = $(this).data('stock');
                const image = $(this).data('image');

                // Isi field
                $('#edit_product_id').val(produkID);
                $('#edit_namaProduk').val(namaProduk);
                $('#edit_category').val(category);
                $('#edit_harga').val(harga);
                $('#edit_stock').val(stock);

                // Tampilkan pratinjau gambar lama
                if (image) {
                    $('#preview_image').attr('src', '/storage/' + image).show();
                } else {
                    $('#preview_image').hide();
                }

                // Set form action
                $('#editProductForm').attr('action', '/admin/products/' + produkID);
                $('#editProductModal').modal('show');
            });
        });
    </script>

    @endpush

    <!-- Modal Tambah User -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Tambah Product baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="container">
                            <div class="row row-cols-2">
                                <div class="col">
                                    <div class="form-group mb-3">
                                        <label for="namaProduk">Masukan Nama Product</label>
                                        <input type="text" name="namaProduk" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group mb-3">
                                        <label for="category">Pilih Kategori</label>
                                        <select name="category" class="form-control" required>
                                            <option value="">-- Pilih Kategori --</option>
                                            <option value="Hot">Hot</option>
                                            <option value="Burger">Burger</option>
                                            <option value="Pizza">Pizza</option>
                                            <option value="Snack">Snack</option>
                                            <option value="Food">Food</option>
                                            <option value="Drink">Drink</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group mb-3">
                                        <label for="harga">Masukan harga</label>
                                        <input type="number" name="harga" class="form-control" required minlength="6">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group mb-3">
                                        <label for="stock">Masukan stock</label>
                                        <input type="number" name="stock" class="form-control" required minlength="6">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group mb-3">
                                        <label for="image">Masukan Gambar Produk</label>
                                        <input type="file" name="image" class="form-control" accept="image/*">
                                        <small class="text-muted">Format: JPG, PNG, WEBP. Maksimal 2MB.</small>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-danger flex-fill me-2" data-dismiss="modal">
                            Membatalkan
                        </button>
                        <button type="submit" class="btn btn-success flex-fill">
                            Konfirmasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <style>
        .modal-custom {
            max-width: 750px;
            /* lebar modal */
        }

        .modal-content {
            height: 450px;
            /* tinggi modal */
        }
    </style>

    <!-- Modal Edit User -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="editProductForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">
                        <input type="hidden" name="product_id" id="edit_product_id">
                        <div class="container">
                            <div class="row row-cols-2">
                                <div class="col">
                                    <div class="form-group mb-3">
                                        <label for="edit_namaProduk">Masukan Nama product</label>
                                        <input type="text" name="namaProduk" id="edit_namaProduk" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group mb-3">
                                        <label for="edit_category">Pilih Kategori</label>
                                        <select name="category" class="form-control" id="edit_category" required>
                                            <option value="">-- Pilih Kategori --</option>
                                            <option value="Hot">Hot</option>
                                            <option value="Burger">Burger</option>
                                            <option value="Pizza">Pizza</option>
                                            <option value="Snack">Snack</option>
                                            <option value="Food">Food</option>
                                            <option value="Drink">Drink</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group mb-3">
                                        <label for="edit_harga">Masukan harga</label>
                                        <input type="number" name="harga" id="edit_harga" class="form-control" required minlength="6">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group mb-3">
                                        <label for="edit_stock">Masukan stock</label>
                                        <input type="number" name="stock" id="edit_stock" class="form-control" required minlength="6">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group mb-3">
                                        <label for="edit_image">Masukan Gambar Produk</label>
                                        <input type="file" name="image" id="edit_image" class="form-control" accept="image/*">
                                        <!-- <img id="preview_image" src="" alt="Preview Gambar" class="mt-2" style="max-width: 100px; display: none;"> -->
                                        <small class="text-muted">Format: JPG, PNG, WEBP. Maksimal 2MB.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-danger flex-fill me-2" data-dismiss="modal">
                            Membatalkan
                        </button>
                        <button type="submit" class="btn btn-success flex-fill">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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

    <!-- Modal Hapus -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-custom">
            <div class="modal-content text-center p-4" style="height: 350px;">
                <div class="modal-body">
                    <!-- Ikon Trash -->
                    <div class="text-danger mb-5">
                        <i class="fas fa-trash fa-8x"></i>
                    </div>
                    <h4 class="fw-bold mb-4">Data akan dihapus dari database</h4>
                    <!-- Tombol -->
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-danger flex-fill me-2" data-dismiss="modal">
                            Membatalkan
                        </button>
                        <button type="submit" id="confirmDeleteBtn" class="btn btn-success flex-fill">
                            Konfirmasi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        let currentFormId = null;

        function openDeleteModal(produkID) {
            currentFormId = 'deleteProductForm' + produkID; // simpan form yang akan di-submit
            var modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            modal.show();
        }

        // Tombol Konfirmasi submit form
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (currentFormId) {
                document.getElementById(currentFormId).submit();
            }
        });
    </script>


    @if(session('success'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
            // Tutup otomatis setelah 2 detik
            setTimeout(() => {
                successModal.hide();
            }, 2000);
        });
    </script>
    @endif
</section>
@endsection