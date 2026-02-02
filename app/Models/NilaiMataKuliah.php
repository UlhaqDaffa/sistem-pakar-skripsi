<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiMataKuliah extends Model
{
    use HasFactory;

    protected $table = 'nilai_mata_kuliah';

    protected $fillable = [
        'konsultasi_id',
        'mata_kuliah_kunci_id',
        'nilai',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
    ];

    public function konsultasi(): BelongsTo
    {
        return $this->belongsTo(Konsultasi::class);
    }

    public function mataKuliahKunci(): BelongsTo
    {
        return $this->belongsTo(MataKuliahKunci::class);
    }
}
