<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KonsultasiDetail extends Model
{
    protected $table = 'konsultasi_detail';

    protected $fillable = [
        'konsultasi_id',
        'pertanyaan_id',
        'opsi_jawaban_id',
    ];

    public function konsultasi(): BelongsTo
    {
        return $this->belongsTo(Konsultasi::class, 'konsultasi_id');
    }

    public function pertanyaan(): BelongsTo
    {
        return $this->belongsTo(Pertanyaan::class, 'pertanyaan_id');
    }

    public function opsiJawaban(): BelongsTo
    {
        return $this->belongsTo(OpsiJawaban::class, 'opsi_jawaban_id');
    }
}
