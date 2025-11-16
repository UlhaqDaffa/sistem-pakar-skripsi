<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataKuliahKunci extends Model
{
    use HasFactory;

    protected $table = 'mata_kuliah_kunci';

    protected $fillable = [
        'kode_mata_kuliah',
        'nama_mata_kuliah',
    ];

    public function nilaiMataKuliah(): HasMany
    {
        return $this->hasMany(NilaiMataKuliah::class);
    }
}
