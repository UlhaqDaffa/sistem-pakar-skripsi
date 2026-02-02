<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriPertanyaan;

class KategoriPertanyaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        KategoriPertanyaan::truncate();

        KategoriPertanyaan::create([
            'kode_kategori' => 'UMUM',
            'nama_kategori' => 'Pertanyaan Umum',
            'tipe' => 'umum',
            'deskripsi' => 'Pertanyaan untuk menentukan arketipe dasar pengguna.',
        ]);

        KategoriPertanyaan::create([
            'kode_kategori' => 'MINAT',
            'nama_kategori' => 'Pertanyaan Minat',
            'tipe' => 'minat',
            'deskripsi' => 'Pertanyaan lanjutan untuk menggali minat spesifik berdasarkan arketipe.',
        ]);

        KategoriPertanyaan::create([
            'kode_kategori' => 'DISK',
            'nama_kategori' => 'Pertanyaan Pembeda',
            'tipe' => 'discriminator',
            'deskripsi' => 'Pertanyaan untuk membedakan kecenderungan arketipe pada minat tertentu.',
        ]);

        KategoriPertanyaan::create([
            'kode_kategori' => 'ASESMEN',
            'nama_kategori' => 'Pertanyaan Asesmen',
            'tipe' => 'asesmen',
            'deskripsi' => 'Pertanyaan untuk mengukur tingkat kemampuan pada bidang yang diminati.',
        ]);
    }
}
