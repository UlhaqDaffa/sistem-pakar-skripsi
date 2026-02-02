<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OpsiJawabanTemplate extends Model
{
    use HasFactory;

    protected $table = 'opsi_jawaban_template';

    protected $fillable = [
        'kode_template',
        'nama_template',
        'deskripsi',
    ];

    /**
     * Relasi ke item-item dalam template
     */
    public function opsiJawabanTemplateItems(): HasMany
    {
        return $this->hasMany(OpsiJawabanTemplateItem::class, 'template_id')->orderBy('urutan');
    }

    /**
     * Relasi many-to-many ke Pertanyaan
     */
    public function pertanyaans(): BelongsToMany
    {
        return $this->belongsToMany(Pertanyaan::class, 'pertanyaan_opsi_jawaban_template', 'template_id', 'pertanyaan_id');
    }
}
