<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonsultasiDetail extends Model
{
    protected $table = 'konsultasi_detail';

    protected $fillable = [
        'konsultasi_id',
        'pertanyaan_id',
        'jawaban_id',
        'nilai_input_pengguna'
    ];
}
