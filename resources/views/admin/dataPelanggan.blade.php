@extends('layouts.adminlte')

@section('title', 'Data Pelanggan')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Data Pelanggan</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Kolom 3: DataTable -->
        <div class="row">
            <div class="col-12">
                <div class="table-section">
                    <table id="users-table" class="table table-striped text-center" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th>Nama Pelanggan</th>
                                <th>Alamat</th>
                                <th>No Telep</th>
                                <th>Waktu Pembelian</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pelanggans as $pelanggan)
                            <tr>
                                <td>{{ $pelanggan->pelangganID }}</td>
                                <td>{{ $pelanggan->namaPelanggan }}</td>
                                <td>{{ $pelanggan->alamat }}</td>
                                <td>{{ $pelanggan->noTelpPelanggan }}</td>
                                <td>{{ $pelanggan->created_at->format('d M Y, h:i') }}</td>
                                <td>
                                    <button href="{{ route('admin.dataPelanggan', $pelanggan->pelangganID) }}"
                                        class="btn btn-sm btn-info" disabled>
                                        <i class="fas fa-eye"></i> Detail
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
    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#users-table').DataTable({

            });
        });
    </script>

    @endpush

</section>
@endsection