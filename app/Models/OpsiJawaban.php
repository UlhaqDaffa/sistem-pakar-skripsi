<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpsiJawaban extends Model
{
    use HasFactory;

    protected $table = 'opsi_jawaban';

    protected $fillable = [
        'skala_jawaban_id',
        'teks_jawaban',
        'nilai',
    ];

    public function jenisJawaban(): BelongsTo
    {
        return $this->belongsTo(JenisJawaban::class, 'jenis_jawaban_id');
    }
}
