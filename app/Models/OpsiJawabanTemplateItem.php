<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpsiJawabanTemplateItem extends Model
{
    use HasFactory;

    protected $table = 'opsi_jawaban_template_item';

    protected $fillable = [
        'template_id',
        'kode_jawaban',
        'teks_jawaban',
        'nilai',
        'urutan',
    ];

    /**
     * Relasi ke template
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(OpsiJawabanTemplate::class, 'template_id');
    }
}
