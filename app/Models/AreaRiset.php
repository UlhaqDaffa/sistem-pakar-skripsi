<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AreaRiset extends Model
{
    use HasFactory;

    protected $table = 'area_riset';

    protected $fillable = [
        'kode_area',
        'nama_area',
        'deskripsi',
        'contoh_studi_kasus',
        'tujuan_masalah',
        'tipe_sistem',
        'target_arketipe',
        'level_kesulitan',
    ];

    protected $casts = [
        'level_kesulitan' => 'integer',
    ];

    public function minatBidangs(): BelongsToMany
    {
        return $this->belongsToMany(MinatBidang::class, 'area_riset_minat_bidang');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'area_riset_tags')->withTimestamps();
    }

    /**
     * Relasi many-to-many ke PolaJudul
     */
    public function polaJuduls(): BelongsToMany
    {
        return $this->belongsToMany(PolaJudul::class, 'area_riset_pola_judul', 'area_riset_id', 'pola_judul_id');
    }
}
