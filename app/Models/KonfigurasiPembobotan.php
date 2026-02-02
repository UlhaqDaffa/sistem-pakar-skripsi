<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KonfigurasiPembobotan extends Model
{
    use HasFactory;

    protected $table = 'konfigurasi_pembobotan';

    protected $fillable = [
        'nama_konfigurasi',
        'bobot_minat',
        'bobot_asesmen',
        'is_active',
    ];

    protected $casts = [
        'bobot_minat' => 'decimal:2',
        'bobot_asesmen' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the active configuration
     */
    public static function getActive(): ?self
    {
        return static::where('is_active', true)->first();
    }
}
