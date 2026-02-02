<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PolaJudul extends Model
{
    use HasFactory;

    protected $table = 'pola_judul';

    protected $fillable = [
        'template_string',
        'deskripsi',
    ];

    /**
     * Relasi many-to-many ke AreaRiset
     */
    public function areaRisets(): BelongsToMany
    {
        return $this->belongsToMany(AreaRiset::class, 'area_riset_pola_judul', 'pola_judul_id', 'area_riset_id');
    }
}
