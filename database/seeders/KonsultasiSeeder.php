<?php

namespace Database\Seeders;

use App\Models\Jawaban;
use App\Models\KategoriPertanyaan;
use App\Models\Pertanyaan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KonsultasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset tables before seeding
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        KategoriPertanyaan::truncate();
        Pertanyaan::truncate();
        Jawaban::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // 1. Kategori: Minat Bidang
        $kategoriMinat = KategoriPertanyaan::create([
            'nama_kategori' => 'Minat Bidang',
            'deskripsi' => 'Pertanyaan untuk mengetahui minat mahasiswa terhadap bidang-bidang dalam teknik informatika.'
        ]);

        $pertanyaanMinat1 = Pertanyaan::create([
            'kategori_id' => $kategoriMinat->id,
            'teks_pertanyaan' => 'Seberapa tertarik Anda dengan bidang Rekayasa Perangkat Lunak (Software Engineering)?',
            'tipe_jawaban' => 'pilihan_ganda',
            'urutan' => 1,
        ]);
        $this->createJawaban($pertanyaanMinat1->id, [
            ['Sangat Tertarik', 4],
            ['Tertarik', 3],
            ['Cukup Tertarik', 2],
            ['Tidak Tertarik', 1],
        ]);

        // 2. Kategori: Asesmen Kemampuan
        $kategoriAsesmen = KategoriPertanyaan::create([
            'nama_kategori' => 'Asesmen Kemampuan',
            'deskripsi' => 'Pertanyaan untuk mengevaluasi pemahaman dan kemampuan teknis mahasiswa.'
        ]);

        $pertanyaanAsesmen1 = Pertanyaan::create([
            'kategori_id' => $kategoriAsesmen->id,
            'teks_pertanyaan' => 'Bagaimana tingkat pemahaman Anda tentang konsep Object-Oriented Programming (OOP)?',
            'tipe_jawaban' => 'pilihan_ganda',
            'urutan' => 1,
        ]);
        $this->createJawaban($pertanyaanAsesmen1->id, [
            ['Sangat Paham', 4],
            ['Paham', 3],
            ['Cukup Paham', 2],
            ['Kurang Paham', 1],
        ]);

        $pertanyaanAsesmen2 = Pertanyaan::create([
            'kategori_id' => $kategoriAsesmen->id,
            'teks_pertanyaan' => 'Seberapa mahir Anda dalam menggunakan sistem basis data dan bahasa SQL?',
            'tipe_jawaban' => 'pilihan_ganda',
            'urutan' => 2,
        ]);
        $this->createJawaban($pertanyaanAsesmen2->id, [
            ['Sangat Mahir', 4],
            ['Mahir', 3],
            ['Cukup Mahir', 2],
            ['Kurang Mahir', 1],
        ]);


        // 3. Kategori: Input Nilai
        $kategoriNilai = KategoriPertanyaan::create([
            'nama_kategori' => 'Input Nilai',
            'deskripsi' => 'Input nilai mata kuliah yang relevan untuk analisis decision tree.'
        ]);

        Pertanyaan::create([
            'kategori_id' => $kategoriNilai->id,
            'teks_pertanyaan' => 'Masukkan nilai mata kuliah "Struktur Data & Algoritma" Anda (skala 0-100).',
            'tipe_jawaban' => 'input_nilai',
            'urutan' => 1,
        ]);

        Pertanyaan::create([
            'kategori_id' => $kategoriNilai->id,
            'teks_pertanyaan' => 'Masukkan nilai mata kuliah "Basis Data" Anda (skala 0-100).',
            'tipe_jawaban' => 'input_nilai',
            'urutan' => 2,
        ]);
    }

    private function createJawaban(int $pertanyaanId, array $jawabanData): void
    {
        foreach ($jawabanData as $data) {
            Jawaban::create([
                'pertanyaan_id' => $pertanyaanId,
                'teks_jawaban' => $data[0],
                'nilai' => $data[1],
            ]);
        }
    }
}