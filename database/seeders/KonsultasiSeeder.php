<?php

namespace Database\Seeders;

use App\Models\KategoriPertanyaan;
use App\Models\OpsiJawaban;
use App\Models\Pertanyaan;
use App\Models\JenisJawaban;
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
        JenisJawaban::truncate();
        OpsiJawaban::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // 1. Skala Jawaban: Minat
        $Minat = JenisJawaban::create([
            'jenis' => 'Minat',
            'deskripsi' => 'Jawaban untuk mengukur tingkat ketertarikan.',
        ]);
        $this->createOpsiJawaban($Minat->id, [
            ['Sangat Tertarik', 4],
            ['Tertarik', 3],
            ['Cukup Tertarik', 2],
            ['Tidak Tertarik', 1],
        ]);

        // 2.  Jawaban: Pemahaman
        $Pemahaman = JenisJawaban::create([
            'jenis' => 'Asesmen',
            'deskripsi' => 'Jawaban untuk mengukur tingkat pemahaman.',
        ]);
        $this->createOpsiJawaban($Pemahaman->id, [
            ['Sangat Paham', 4],
            ['Paham', 3],
            ['Cukup Paham', 2],
            ['Kurang Paham', 1],
        ]);

        // 3.  Jawaban: Nilai
        $Nilai = JenisJawaban::create([
            'jenis' => 'Nilai',
            'deskripsi' => 'Jawaban untuk input nilai A-E.',
        ]);
        $this->createOpsiJawaban($Nilai->id, [
            ['A', 5],
            ['B', 4],
            ['C', 3],
            ['D', 2],
            ['E', 1],
        ]);

        // Kategori 1: Minat Bidang
        $kategoriMinat = KategoriPertanyaan::create([
            'nama_kategori' => 'Minat Bidang',
            'deskripsi' => 'Pertanyaan untuk mengetahui minat mahasiswa terhadap bidang-bidang dalam teknik informatika.'
        ]);

        Pertanyaan::create([
            'kategori_id' => $kategoriMinat->id,
            'jenis_jawaban_id' => $Minat->id,
            'teks_pertanyaan' => 'Seberapa tertarik Anda dengan bidang Rekayasa Perangkat Lunak (Software Engineering)?',
            'tipe_jawaban' => 'pilihan_ganda',
            'urutan' => 1,
        ]);

        Pertanyaan::create([
            'kategori_id' => $kategoriMinat->id,
            'jenis_jawaban_id' => $Minat->id,
            'teks_pertanyaan' => 'Seberapa tertarik Anda dengan bidang pengembangan aplikasi web?',
            'tipe_jawaban' => 'pilihan_ganda',
            'urutan' => 2,
        ]);

        // Kategori 2: Asesmen Kemampuan
        $kategoriAsesmen = KategoriPertanyaan::create([
            'nama_kategori' => 'Asesmen Kemampuan',
            'deskripsi' => 'Pertanyaan untuk mengevaluasi pemahaman dan kemampuan teknis mahasiswa.'
        ]);

        Pertanyaan::create([
            'kategori_id' => $kategoriAsesmen->id,
            'jenis_jawaban_id' => $Pemahaman->id,
            'teks_pertanyaan' => 'Bagaimana tingkat pemahaman Anda tentang konsep Object-Oriented Programming (OOP)?',
            'tipe_jawaban' => 'pilihan_ganda',
            'urutan' => 1,
        ]);

        Pertanyaan::create([
            'kategori_id' => $kategoriAsesmen->id,
            'jenis_jawaban_id' => $Pemahaman->id,
            'teks_pertanyaan' => 'Seberapa mahir Anda dalam menggunakan sistem basis data dan bahasa SQL?',
            'tipe_jawaban' => 'pilihan_ganda',
            'urutan' => 2,
        ]);

        // Kategori 3: Input Nilai
        $kategoriNilai = KategoriPertanyaan::create([
            'nama_kategori' => 'Input Nilai',
            'deskripsi' => 'Input nilai mata kuliah terakhir anda'
        ]);

        Pertanyaan::create([
            'kategori_id' => $kategoriNilai->id,
            'jenis_jawaban_id' => $Nilai->id,
            'teks_pertanyaan' => 'Masukkan nilai mata kuliah Struktur Data & Algoritma Anda (A-B-C-D-E).',
            'tipe_jawaban' => 'input_nilai',
            'urutan' => 1,
        ]);

        Pertanyaan::create([
            'kategori_id' => $kategoriNilai->id,
            'jenis_jawaban_id' => $Nilai->id,
            'teks_pertanyaan' => 'Masukkan nilai mata kuliah Pemrograman Anda (A-B-C-D-E).',
            'tipe_jawaban' => 'input_nilai',
            'urutan' => 2,
        ]);

        Pertanyaan::create([
            'kategori_id' => $kategoriNilai->id,
            'jenis_jawaban_id' => $Nilai->id,
            'teks_pertanyaan' => 'Masukkan nilai mata kuliah Jaringan Komputer Anda (A-B-C-D-E).',
            'tipe_jawaban' => 'input_nilai',
            'urutan' => 3,
        ]);

        Pertanyaan::create([
            'kategori_id' => $kategoriNilai->id,
            'jenis_jawaban_id' => $Nilai->id,
            'teks_pertanyaan' => 'Masukkan nilai mata kuliah Kecerdasan Buatan Anda (A-B-C-D-E).',
            'tipe_jawaban' => 'input_nilai',
            'urutan' => 4,
        ]);

        Pertanyaan::create([
            'kategori_id' => $kategoriNilai->id,
            'jenis_jawaban_id' => $Nilai->id,
            'teks_pertanyaan' => 'Masukkan nilai mata kuliah Basis Data Anda (A-B-C-D-E).',
            'tipe_jawaban' => 'input_nilai',
            'urutan' => 5,
        ]);
    }

    private function createOpsiJawaban(int $skalaId, array $opsiData): void
    {
        foreach ($opsiData as $data) {
            OpsiJawaban::create([
                'jenis_jawaban_id' => $skalaId,
                'teks_jawaban' => $data[0],
                'nilai' => $data[1],
            ]);
        }
    }
}
