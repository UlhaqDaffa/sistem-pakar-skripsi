<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pertanyaan extends Model
{
    protected $table = 'pertanyaan';

    public function kategori()
    {
        return $this->belongsTo(KategoriPertanyaan::class);
    }

    public function jawaban()
    {
        return $this->hasMany(Jawaban::class);
    }
}
