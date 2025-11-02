<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $primaryKey = 'produkID';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'namaProduk',
        'image',
        'category',
        'stock',
        'harga'
    ];
}