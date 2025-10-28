<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AreaRiset extends Model
{
    use HasFactory;

    protected $table = 'area_riset';

    protected $fillable = [
        'kode_area',
        'minat_bidang_id',
        'nama_area',
        'deskripsi',
        'kata_kunci_teknologi',
        'kata_kunci_metode',
        'contoh_studi_kasus',
    ];

    public function minatBidang(): BelongsTo
    {
        return $this->belongsTo(MinatBidang::class);
    }
}
