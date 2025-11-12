<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    protected $table = 'detailPenjualans';
    protected $primaryKey = 'detailID';

    protected $fillable = [
        'penjualanID',
        'produkID',
        'jumlahProduk',
        'subTotal'
    ];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'penjualanID', 'penjualanID');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'produkID', 'produkID');
    }
}