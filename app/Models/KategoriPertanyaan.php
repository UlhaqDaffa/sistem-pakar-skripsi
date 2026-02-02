<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriPertanyaan extends Model
{
    use HasFactory;

    protected $table = 'kategori_pertanyaan';

    protected $fillable = [
        'kode_kategori',
        'nama_kategori',
        'tipe',
        'deskripsi',
    ];

    public function pertanyaans(): HasMany
    {
        return $this->hasMany(Pertanyaan::class, 'kategori_id');
    }
}
