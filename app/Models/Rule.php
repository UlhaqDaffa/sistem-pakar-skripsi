<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rule extends Model
{
    use HasFactory;

    protected $table = 'rules';

    protected $fillable = [
        'kode_rule',
        'nama_rule',
        'deskripsi',
        'kondisi',
        'aksi',
        'minat_bidang_id',
        'area_riset_id',
        'prioritas',
        'is_active',
    ];

    protected $casts = [
        'kondisi' => 'array',
        'aksi' => 'array',
        'is_active' => 'boolean',
        'prioritas' => 'integer',
    ];

    /**
     * Relasi ke MinatBidang
     */
    public function minatBidang(): BelongsTo
    {
        return $this->belongsTo(MinatBidang::class);
    }

    /**
     * Relasi ke AreaRiset
     */
    public function areaRiset(): BelongsTo
    {
        return $this->belongsTo(AreaRiset::class);
    }

    /**
     * Scope untuk rules aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk urut berdasarkan prioritas
     */
    public function scopeOrderedByPriority($query)
    {
        return $query->orderBy('prioritas', 'asc');
    }
}
