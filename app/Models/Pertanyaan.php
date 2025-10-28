<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pertanyaan extends Model
{
    use HasFactory;

    protected $table = 'pertanyaan';

    protected $fillable = [
        'kategori_id',
        'kode_pertanyaan',
        'teks_pertanyaan',
        'is_start_point',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriPertanyaan::class, 'kategori_id');
    }

    public function opsiJawaban(): HasMany
    {
        return $this->hasMany(OpsiJawaban::class);
    }
}
