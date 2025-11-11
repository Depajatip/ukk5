@extends('layouts.kasirlayout')

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
                                <h4>dummy -</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card p-3 text-center"
                                style="height: 150px; align-items: center; justify-content: center;">
                                <h3>Total Admin</h3>
                                <h4>dummy -</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card p-3 text-center"
                                style="height: 150px; align-items: center; justify-content: center;">
                                <h3>Total Kasir</h3>
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
                                    <th class="text-center">User ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Register Date</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                
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
            </script>

        @endpush
        
    </section>
@endsection