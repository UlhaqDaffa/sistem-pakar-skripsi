<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MinatBidang extends Model
{
    use HasFactory;

    protected $table = 'minat_bidang';

    protected $fillable = [
        'kode_bidang',
        'nama_bidang',
        'deskripsi',
    ];

    public function areaRisets(): HasMany
    {
        return $this->hasMany(AreaRiset::class);
    }
}
