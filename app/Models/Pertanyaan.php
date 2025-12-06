<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Pertanyaan extends Model
{
    use HasFactory;

    protected $table = 'pertanyaan';

    protected $fillable = [
        'kategori_id',
        'minat_bidang_id',
        'kode_pertanyaan',
        'teks_pertanyaan',
        'is_start_point',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriPertanyaan::class, 'kategori_id');
    }

    /**
     * Relasi ke MinatBidang (opsional, hanya untuk pertanyaan yang terkait minat spesifik)
     */
    public function minatBidang(): BelongsTo
    {
        return $this->belongsTo(MinatBidang::class, 'minat_bidang_id');
    }

    /**
     * Relasi many-to-many ke OpsiJawabanTemplate
     */
    public function opsiJawabanTemplate(): BelongsToMany
    {
        return $this->belongsToMany(OpsiJawabanTemplate::class, 'pertanyaan_opsi_jawaban_template', 'pertanyaan_id', 'template_id');
    }

    /**
     * Relasi lama ke OpsiJawaban (untuk backward compatibility dengan data historis)
     * @deprecated Gunakan opsiJawaban attribute yang sudah backward compatible
     */
    public function opsiJawaban(): HasMany
    {
        return $this->hasMany(OpsiJawaban::class);
    }

    /**
     * Accessor untuk backward compatibility
     * Mengembalikan opsi jawaban dari template jika ada, jika tidak fallback ke opsi lama
     */
    public function getOpsiJawabanAttribute(): Collection
    {
        // Cek apakah pertanyaan memiliki template (eager load jika belum)
        if (!$this->relationLoaded('opsiJawabanTemplate')) {
            $this->load('opsiJawabanTemplate.opsiJawabanTemplateItems');
        }
        
        $template = $this->opsiJawabanTemplate->first();
        
        if ($template && $template->opsiJawabanTemplateItems->isNotEmpty()) {
            // Jika ada template, kembalikan item-item template sebagai collection yang mirip dengan OpsiJawaban
            return $template->opsiJawabanTemplateItems->map(function ($item) {
                // Buat object yang mirip dengan OpsiJawaban untuk backward compatibility
                return (object) [
                    'id' => $item->id,
                    'pertanyaan_id' => $this->id,
                    'kode_jawaban' => $item->kode_jawaban,
                    'teks_jawaban' => $item->teks_jawaban,
                    'nilai' => $item->nilai,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            });
        }

        // Fallback ke opsi jawaban lama (untuk data historis)
        if (!$this->relationLoaded('opsiJawaban')) {
            $this->load('opsiJawaban');
        }
        return $this->getRelationValue('opsiJawaban') ?? collect();
    }
}
