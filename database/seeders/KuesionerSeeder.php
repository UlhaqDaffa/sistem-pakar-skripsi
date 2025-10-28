<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriPertanyaan;
use App\Models\Pertanyaan;
use App\Models\OpsiJawaban;
use Illuminate\Support\Facades\DB;

class KuesionerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan pengecekan foreign key untuk truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Hapus data lama untuk menghindari duplikasi
        OpsiJawaban::truncate();
        Pertanyaan::truncate();
        KategoriPertanyaan::truncate();

        // Aktifkan kembali pengecekan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Buat Kategori Pertanyaan
        $umum = KategoriPertanyaan::create([
            'kode_kategori' => 'UMUM',
            'nama_kategori' => 'Pertanyaan Umum',
            'tipe' => 'umum',
            'deskripsi' => 'Pertanyaan untuk menentukan arketipe dasar pengguna.',
        ]);

        $minat = KategoriPertanyaan::create([
            'kode_kategori' => 'MINAT',
            'nama_kategori' => 'Pertanyaan Minat',
            'tipe' => 'minat',
            'deskripsi' => 'Pertanyaan lanjutan untuk menggali minat spesifik berdasarkan arketipe.',
        ]);

        $asesmen = KategoriPertanyaan::create([
            'kode_kategori' => 'ASESMEN',
            'nama_kategori' => 'Pertanyaan Asesmen',
            'tipe' => 'asesmen',
            'deskripsi' => 'Pertanyaan untuk mengukur tingkat kemampuan pada bidang yang diminati.',
        ]);

        // 2. Buat Pertanyaan dan Opsi Jawaban

        // === TAHAP 1: PERTANYAAN UMUM (ARKETIPE) ===
        $p1 = Pertanyaan::create([
            'kategori_id' => $umum->id,
            'kode_pertanyaan' => 'ARKETIPE_01',
            'teks_pertanyaan' => 'Dari aktivitas berikut, manakah yang paling menggambarkan diri Anda atau paling Anda nikmati?',
            'is_start_point' => true,
        ]);

        OpsiJawaban::create([
            'pertanyaan_id' => $p1->id,
            'kode_jawaban' => 'ARKETIPE_CREATOR',
            'teks_jawaban' => 'Saya suka merancang dan membangun sesuatu yang baru dari awal, seperti membuat aplikasi, website, atau karya visual.',
            'nilai' => 1, // Nilai bisa digunakan untuk identifikasi
        ]);
        OpsiJawaban::create([
            'pertanyaan_id' => $p1->id,
            'kode_jawaban' => 'ARKETIPE_ANALIS',
            'teks_jawaban' => 'Saya menikmati proses menganalisis informasi, menemukan pola tersembunyi, dan memecahkan masalah yang rumit.',
            'nilai' => 2,
        ]);
        OpsiJawaban::create([
            'pertanyaan_id' => $p1->id,
            'kode_jawaban' => 'ARKETIPE_ARCHITECT',
            'teks_jawaban' => 'Saya tertarik dalam merancang sistem yang besar dan kompleks, memastikan semua komponen dapat terhubung dan bekerja secara efisien.',
            'nilai' => 3,
        ]);


        // === TAHAP 2: PERTANYAAN MINAT (BERDASARKAN ARKETIPE) ===

        // -- Pertanyaan untuk Arketipe Creator --
        $p_minat_creator = Pertanyaan::create([
            'kategori_id' => $minat->id,
            'kode_pertanyaan' => 'MINAT_CREATOR_01',
            'teks_pertanyaan' => 'Sebagai seorang "Creator", platform atau bidang apa yang paling menarik minat Anda untuk menciptakan sesuatu?',
        ]);
        OpsiJawaban::create([
            'pertanyaan_id' => $p_minat_creator->id,
            'kode_jawaban' => 'MINAT_WEB',
            'teks_jawaban' => 'Pengembangan Web (Website, Aplikasi Web)',
            'nilai' => 1,
        ]);
        OpsiJawaban::create([
            'pertanyaan_id' => $p_minat_creator->id,
            'kode_jawaban' => 'MINAT_MOBILE',
            'teks_jawaban' => 'Pengembangan Mobile (Aplikasi Android/iOS)',
            'nilai' => 2,
        ]);
        OpsiJawaban::create([
            'pertanyaan_id' => $p_minat_creator->id,
            'kode_jawaban' => 'MINAT_GAME_DEV',
            'teks_jawaban' => 'Pengembangan Game',
            'nilai' => 3,
        ]);

        // -- Pertanyaan untuk Arketipe Analis --
        $p_minat_analis = Pertanyaan::create([
            'kategori_id' => $minat->id,
            'kode_pertanyaan' => 'MINAT_ANALIS_01',
            'teks_pertanyaan' => 'Sebagai seorang "Analis", jenis analisis atau data apa yang paling membuat Anda penasaran?',
        ]);
        OpsiJawaban::create([
            'pertanyaan_id' => $p_minat_analis->id,
            'kode_jawaban' => 'MINAT_DATASCI',
            'teks_jawaban' => 'Ilmu Data (Machine Learning, AI, Big Data)',
            'nilai' => 1,
        ]);
        OpsiJawaban::create([
            'pertanyaan_id' => $p_minat_analis->id,
            'kode_jawaban' => 'MINAT_BI',
            'teks_jawaban' => 'Business Intelligence (Analisis Data Bisnis)',
            'nilai' => 2,
        ]);
        OpsiJawaban::create([
            'pertanyaan_id' => $p_minat_analis->id,
            'kode_jawaban' => 'MINAT_SECURITY',
            'teks_jawaban' => 'Keamanan Siber (Cyber Security)',
            'nilai' => 3,
        ]);

        // -- Pertanyaan untuk Arketipe Architect --
        $p_minat_architect = Pertanyaan::create([
            'kategori_id' => $minat->id,
            'kode_pertanyaan' => 'MINAT_ARCHITECT_01',
            'teks_pertanyaan' => 'Sebagai seorang "Architect", arsitektur sistem di level mana yang paling ingin Anda rancang?',
        ]);
        OpsiJawaban::create([
            'pertanyaan_id' => $p_minat_architect->id,
            'kode_jawaban' => 'MINAT_CLOUD',
            'teks_jawaban' => 'Arsitektur Cloud (Cloud Infrastructure)',
            'nilai' => 1,
        ]);
        OpsiJawaban::create([
            'pertanyaan_id' => $p_minat_architect->id,
            'kode_jawaban' => 'MINAT_NETWORK',
            'teks_jawaban' => 'Arsitektur Jaringan (Network Infrastructure)',
            'nilai' => 2,
        ]);
        OpsiJawaban::create([
            'pertanyaan_id' => $p_minat_architect->id,
            'kode_jawaban' => 'MINAT_DATA_ARCH',
            'teks_jawaban' => 'Arsitektur Data (Data Engineering)',
            'nilai' => 3,
        ]);


        // === TAHAP 3: PERTANYAAN ASESMEN (BERDASARKAN MINAT) ===
        // Helper untuk membuat pertanyaan asesmen dengan skala Likert
        $createAsesmenQuestion = function ($kode, $teks) use ($asesmen) {
            $pertanyaan = Pertanyaan::create([
                'kategori_id' => $asesmen->id,
                'kode_pertanyaan' => $kode,
                'teks_pertanyaan' => $teks,
            ]);
            $skala = [
                1 => 'Sangat Tidak Paham',
                2 => 'Tidak Paham',
                3 => 'Cukup Paham',
                4 => 'Paham',
                5 => 'Sangat Paham',
            ];
            foreach ($skala as $nilai => $jawaban) {
                OpsiJawaban::create([
                    'pertanyaan_id' => $pertanyaan->id,
                    'kode_jawaban' => 'SKALA_' . $nilai,
                    'teks_jawaban' => $jawaban,
                    'nilai' => $nilai,
                ]);
            }
        };

        // -- Asesmen untuk WEB_DEV --
        $createAsesmenQuestion('ASESMEN_WEB_01', 'Seberapa paham Anda tentang HTML5 dan struktur semantiknya?');
        $createAsesmenQuestion('ASESMEN_WEB_02', 'Seberapa mahir Anda dalam styling menggunakan CSS3, termasuk Flexbox dan Grid?');
        $createAsesmenQuestion('ASESMEN_WEB_03', 'Seberapa dalam pemahaman Anda tentang JavaScript (ES6+), termasuk konsep asynchronous?');
        $createAsesmenQuestion('ASESMEN_WEB_04', 'Seberapa familiar Anda dengan salah satu framework JavaScript front-end (React, Vue, atau Angular)?');
        $createAsesmenQuestion('ASESMEN_WEB_05', 'Seberapa berpengalaman Anda dengan bahasa pemrograman back-end (misal: PHP, Node.js, Python)?');

        // -- Asesmen untuk MOBILE_DEV --
        $createAsesmenQuestion('ASESMEN_MOBILE_01', 'Seberapa paham Anda tentang dasar-dasar pengembangan aplikasi mobile (native vs cross-platform)?');
        $createAsesmenQuestion('ASESMEN_MOBILE_02', 'Seberapa familiar Anda dengan bahasa Kotlin (untuk Android) atau Swift (untuk iOS)?');
        $createAsesmenQuestion('ASESMEN_MOBILE_03', 'Seberapa berpengalaman Anda dengan framework cross-platform seperti Flutter atau React Native?');
        $createAsesmenQuestion('ASESMEN_MOBILE_04', 'Seberapa paham Anda tentang siklus hidup (lifecycle) sebuah aplikasi mobile?');
        $createAsesmenQuestion('ASESMEN_MOBILE_05', 'Seberapa mengerti Anda tentang cara mengelola state dalam aplikasi mobile?');

        // -- Asesmen untuk DATA_SCIENCE --
        $createAsesmenQuestion('ASESMEN_DATASCI_01', 'Seberapa kuat pemahaman Anda tentang statistika dasar dan probabilitas?');
        $createAsesmenQuestion('ASESMEN_DATASCI_02', 'Seberapa mahir Anda menggunakan Python untuk analisis data (dengan library seperti Pandas, NumPy)?');
        $createAsesmenQuestion('ASESMEN_DATASCI_03', 'Seberapa familiar Anda dengan konsep dasar Machine Learning (supervised vs unsupervised learning)?');
        $createAsesmenQuestion('ASESMEN_DATASCI_04', 'Seberapa berpengalaman Anda dalam membersihkan dan mempersiapkan data (Data Cleaning & Preprocessing)?');
        $createAsesmenQuestion('ASESMEN_DATASCI_05', 'Seberapa paham Anda tentang algoritma klasifikasi atau regresi (contoh: Logistic Regression, Decision Tree)?');
    }
}
