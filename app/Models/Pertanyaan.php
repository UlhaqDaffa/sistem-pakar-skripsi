<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pertanyaan extends Model
{
    protected $table = 'pertanyaan';

    protected $fillable = [
        'kategori_id',
        'jenis_jawaban_id',
        'teks_pertanyaan',
        'tipe_jawaban',
        'urutan',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriPertanyaan::class, 'kategori_id');
    }

    public function jenisJawaban(): BelongsTo
    {
        return $this->belongsTo(JenisJawaban::class, 'jenis_jawaban_id');
    }

    public function opsiJawaban(): HasMany
    {
        return $this->hasMany(OpsiJawaban::class, 'jenis_jawaban_id', 'jenis_jawaban_id');
    }
}
