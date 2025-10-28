<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Konsultasi extends Model
{
    use HasFactory;

    protected $table = 'konsultasi';

    protected $fillable = [
        'user_id',
        'status',
        'hasil_minat_id',
        'hasil_akademik_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasilMinat(): BelongsTo
    {
        return $this->belongsTo(AreaRiset::class, 'hasil_minat_id');
    }

    public function hasilAkademik(): BelongsTo
    {
        return $this->belongsTo(AreaRiset::class, 'hasil_akademik_id');
    }

    public function jawabanKonsultasis(): HasMany
    {
        return $this->hasMany(JawabanKonsultasi::class);
    }
}
