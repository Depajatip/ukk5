@extends('layouts.adminlte')

@section('title', 'manageuser')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manage User</h1>
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
                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#addUserModal">
                            <i class="fas fa-plus"></i> Tambah User
                        </button>
                    </div>
                </div>
            </div>

            <!-- Kolom 2: Card Statistik -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card-section d-flex g-3">
                        <div class="col-md-4">
                            <div class="card p-3 text-center"
                                style="height: 150px; align-items: center; justify-content: center;">
                                <h3>Total User</h3>
                                <h4>{{ $totalUser }} -</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card p-3 text-center"
                                style="height: 150px; align-items: center; justify-content: center;">
                                <h3>Total Admin</h3>
                                <h4>{{ $totalAdmin }} -</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card p-3 text-center"
                                style="height: 150px; align-items: center; justify-content: center;">
                                <h3>Total Kasir</h3>
                                <h4>{{ $totalCashier }} -</h4>
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
                                    <th class="text-center">User ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Register Date</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td class="text-center">{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->created_at->format('d M, Y') }}</td>
                                        <td>{{ ucfirst($user->role ?? 'user') }}</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-primary edit-btn" data-id="{{ $user->id }}"
                                                data-name="{{ $user->name }}" data-email="{{ $user->email }}"
                                                data-role="{{ $user->role }}" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form id="deleteUserForm{{ $user->id }}"
                                                action="{{ route('admin.user.destroy', $user->id) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger" title="Hapus"
                                                    onclick="openDeleteModal({{ $user->id }})">
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
                $(document).ready(function () {
                    $('#users-table').DataTable({

                    });
                });
                $(document).ready(function () {
                    // Isi modal saat tombol edit diklik
                    $('.edit-btn').on('click', function () {
                        const id = $(this).data('id');
                        const name = $(this).data('name');
                        const email = $(this).data('email');
                        const role = $(this).data('role');

                        $('#edit_user_id').val(id);
                        $('#edit_name').val(name);
                        $('#edit_email').val(email);
                        $('#edit_role').val(role);
                        $('#edit_password').val(''); // reset password field


                        $('#editUserForm').attr('action', `/admin/user/${id}`);
                        $('#editUserModal').modal('show');
                    });
                });
            </script>

        @endpush
        <!-- Modal Tambah User -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-custom">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">Tambah User Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.user.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="container">
                                <div class="row row-cols-2">
                                    <div class="col">
                                        <div class="form-group mb-3">
                                            <label for="name">Masukan Nama</label>
                                            <input type="text" name="name" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group mb-3">
                                            <label for="email">Masukan Email</label>
                                            <input type="email" name="email" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group mb-3">
                                            <label for="password">Password</label>
                                            <input type="password" name="password" class="form-control" required
                                                minlength="6">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group mb-3">
                                            <label for="role">Pilih Role</label>
                                            <select name="role" class="form-control" required>
                                                <option value="">-- Pilih Role --</option>
                                                <option value="admin">Admin</option>
                                                <option value="cashier">Cashier</option>
                                            </select>
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
                height: 350px;
                /* tinggi modal */
            }
        </style>
        <!-- Modal Edit User -->
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-custom">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form id="editUserForm" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-body">
                            <input type="hidden" name="user_id" id="edit_user_id">
                            <div class="container">
                                <div class="row row-cols-2">
                                    <div class="col">
                                        <div class="form-group mb-3">
                                            <label for="edit_name">Masukan Nama</label>
                                            <input type="text" name="name" id="edit_name" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group mb-3">
                                            <label for="edit_email">Masukan Email</label>
                                            <input type="email" name="email" id="edit_email" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group mb-3">
                                            <label for="edit_password">Password (isi jika ingin ganti)</label>
                                            <input type="password" name="password" id="edit_password" class="form-control"
                                                minlength="6">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group mb-3">
                                            <label for="edit_role">Pilih Role</label>
                                            <select name="role" id="edit_role" class="form-control" required>
                                                <option value="">-- Pilih Role --</option>
                                                <option value="admin">Admin</option>
                                                <option value="cashier">Cashier</option>
                                            </select>
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
                <div class="modal-content text-center p-4">
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
                <div class="modal-content text-center p-4">
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
                            <button type="button" id="confirmDeleteBtn" class="btn btn-success flex-fill">
                                Konfirmasi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            let currentFormId = null;

            function openDeleteModal(userId) {
                currentFormId = 'deleteUserForm' + userId; // simpan form yang akan di-submit
                var modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
                modal.show();
            }

            // Tombol Konfirmasi submit form
            document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
                if (currentFormId) {
                    document.getElementById(currentFormId).submit();
                }
            });
        </script>


        @if(session('success'))
            <script>
                document.addEventListener("DOMContentLoaded", function () {
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