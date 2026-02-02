<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MinatBidang;
use Illuminate\Support\Facades\DB;

class MinatBidangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        MinatBidang::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Rekayasa Perangkat Lunak (Software Engineering)
        MinatBidang::create([
            'kode_bidang' => 'RPL',
            'nama_bidang' => 'Rekayasa Perangkat Lunak',
            'deskripsi' => 'Fokus pada proses, metodologi, arsitektur, dan pengujian untuk membangun sistem perangkat lunak yang kompleks, andal, dan scalable. Ini adalah bidang bagi mereka yang menyukai "cara" membangun software dengan benar.',
        ]);

        // 2. Pengembangan Aplikasi (Application Development)
        MinatBidang::create([
            'kode_bidang' => 'PENG',
            'nama_bidang' => 'Pengembangan Aplikasi',
            'deskripsi' => 'Fokus pada implementasi teknis dan framework spesifik untuk menciptakan aplikasi yang digunakan langsung oleh end-user. Bidang ini lebih fokus pada "hasil" (produk) daripada prosesnya.',
        ]);

        // 3. Kecerdasan Buatan (Artificial Intelligence)
        MinatBidang::create([
            'kode_bidang' => 'AI',
            'nama_bidang' => 'Kecerdasan Buatan',
            'deskripsi' => 'Fokus pada penciptaan sistem yang dapat meniru kecerdasan manusia, seperti belajar dari data, membuat keputusan, dan memahami lingkungan. Ini adalah bidang untuk "Analyst" yang ingin membuat mesin "berpikir".',
        ]);

        // 4. Sains Data & Big Data (Data Science & Big Data)
        MinatBidang::create([
            'kode_bidang' => 'DATA',
            'nama_bidang' => 'Sains Data & Big Data',
            'deskripsi' => 'Beririsan dengan AI, namun lebih fokus pada proses penggalian wawasan (insight), pola, dan pengetahuan dari kumpulan data dalam skala besar (big data) untuk mendukung pengambilan keputusan.',
        ]);

        // 5. Pemrosesan Citra & Visi Komputer (Image Processing & Computer Vision)
        MinatBidang::create([
            'kode_bidang' => 'CITRA',
            'nama_bidang' => 'Pemrosesan Citra & Visi Komputer',
            'deskripsi' => 'Cabang spesifik dari AI yang berfokus pada bagaimana komputer dapat "melihat" dan menginterpretasi informasi dari dunia visual (gambar, video).',
        ]);

        // 6. Pemrosesan Bahasa Alami (Natural Language Processing - NLP)
        MinatBidang::create([
            'kode_bidang' => 'NLP',
            'nama_bidang' => 'Pemrosesan Bahasa Alami',
            'deskripsi' => 'Cabang spesifik dari AI yang berfokus pada interaksi antara komputer dan bahasa manusia, memungkinkan mesin untuk membaca, memahami, dan menghasilkan teks atau ucapan.',
        ]);

        // 7. Desain UI/UX & Interaksi Manusia-Komputer (UI/UX & HCI)
        MinatBidang::create([
            'kode_bidang' => 'HCI',
            'nama_bidang' => 'Desain UI/UX & Interaksi Manusia-Komputer',
            'deskripsi' => 'Fokus pada perancangan sistem yang tidak hanya fungsional, tetapi juga mudah, efisien, dan menyenangkan untuk digunakan. Ini menjembatani psikologi manusia dengan desain antarmuka.',
        ]);

        // 8. Grafika Komputer & Multimedia (Computer Graphics & Multimedia)
        MinatBidang::create([
            'kode_bidang' => 'GRAF',
            'nama_bidang' => 'Grafika Komputer & Multimedia',
            'deskripsi' => 'Fokus pada pembuatan, pemrosesan, dan rendering gambar visual, animasi, dan konten interaktif. Seringkali menjadi dasar untuk game development dan simulasi.',
        ]);

        // 9. Jaringan & Keamanan Siber (Networking & Cybersecurity)
        MinatBidang::create([
            'kode_bidang' => 'JAR',
            'nama_bidang' => 'Jaringan & Keamanan Siber',
            'deskripsi' => 'Fokus pada desain, manajemen, dan pengamanan infrastruktur komunikasi data. Ini mencakup segala hal mulai dari koneksi internet hingga perlindungan data dari peretas.',
        ]);

        // 10. Sistem Tertanam & Internet of Things (Embedded Systems & IoT)
        MinatBidang::create([
            'kode_bidang' => 'IOT',
            'nama_bidang' => 'Sistem Tertanam & Internet of Things',
            'deskripsi' => 'Fokus pada perancangan sistem komputasi yang terintegrasi langsung dengan perangkat fisik (mikrokontroler, sensor) dan sering terhubung ke internet untuk mengumpulkan atau mengirim data.',
        ]);
    }
}
