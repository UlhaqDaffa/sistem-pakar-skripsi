<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public function areaRisets(): BelongsToMany
    {
        return $this->belongsToMany(AreaRiset::class, 'area_riset_minat_bidang');
    }

    /**
     * Relasi ke Pertanyaan yang menggunakan minat ini (asesmen, discriminator, dll)
     */
    public function pertanyaans(): HasMany
    {
        return $this->hasMany(Pertanyaan::class, 'minat_bidang_id');
    }
}
