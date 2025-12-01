<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_tag',
        'tipe',
    ];

    public function areaRisets(): BelongsToMany
    {
        return $this->belongsToMany(AreaRiset::class, 'area_riset_tags')->withTimestamps();
    }
}








