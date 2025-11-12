<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggans';
    protected $primaryKey = 'pelangganID';

    protected $fillable = [
        'namaPelanggan',
        'alamat',
        'noTelpPelanggan'
    ];

    // Opsional: relasi
    public function penjualans()
    {
        return $this->hasMany(Penjualan::class, 'pelangganID', 'pelangganID');
    }
}