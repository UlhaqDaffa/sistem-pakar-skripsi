<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PilganDetail extends Model
{
    protected $table = 'pilgan_detail';

    protected $fillable = [
        'konsultasi_id',
        'pertanyaan_id',
        'jawaban_id',
    ];
}
