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
                                <h3>dummy</h3>
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
                                    <th>Waktu Pemesanan</th>
                                    <th>Total Harga</th>
                                    <th>Status</th>
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