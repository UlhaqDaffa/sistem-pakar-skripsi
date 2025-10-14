<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataKuliahKunci extends Model
{
    use HasFactory;

    protected $table = 'mata_kuliah_kunci';

    protected $fillable = ['nama_mata_kuliah'];

    public $timestamps = false;
}
