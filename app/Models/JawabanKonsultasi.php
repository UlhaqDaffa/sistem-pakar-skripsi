<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JawabanKonsultasi extends Model
{
    use HasFactory;

    protected $table = 'jawaban_konsultasi';

    public $timestamps = false;

    protected $fillable = [
        'konsultasi_id',
        'opsi_jawaban_id',
    ];

    public function konsultasi(): BelongsTo
    {
        return $this->belongsTo(Konsultasi::class);
    }

    public function opsiJawaban(): BelongsTo
    {
        return $this->belongsTo(OpsiJawaban::class);
    }
}
