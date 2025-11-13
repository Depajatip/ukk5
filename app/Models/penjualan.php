<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualans';
    protected $primaryKey = 'penjualanID';

    protected $fillable = [
        'pelangganID',
        'totalHarga',
        'kodePesanan',
        'status',
        'diskon',
        'uangBayar',
        'kembalian',
    ];

public function pelanggan()
{
    return $this->belongsTo(\App\Models\Pelanggan::class, 'pelangganID', 'pelangganID');
}

    public function details()
    {
        return $this->hasMany(DetailPenjualan::class, 'penjualanID', 'penjualanID');
    }
}
