<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisJawaban extends Model
{
    use HasFactory;

    protected $table = 'jenis_jawaban';

    protected $fillable = [
        'jenis',
        'deskripsi',
    ];

    public function opsiJawaban(): HasMany
    {
        return $this->hasMany(OpsiJawaban::class, 'jenis_jawaban_id');
    }
}
