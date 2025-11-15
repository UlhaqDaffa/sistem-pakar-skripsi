<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MinatBidang;
use App\Models\AreaRiset;
use Illuminate\Support\Facades\DB;

class MinatDanRisetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        AreaRiset::truncate();
        MinatBidang::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Buat Minat Bidang (Kategori Luas)
        $rpl = MinatBidang::create([
            'kode_bidang' => 'RPL',
            'nama_bidang' => 'Rekayasa Perangkat Lunak',
            'deskripsi' => 'Disiplin ilmu yang membahas semua aspek produksi perangkat lunak, mulai dari tahap awal spesifikasi sistem hingga pemeliharaan sistem setelah digunakan.',
        ]);

        $ai = MinatBidang::create([
            'kode_bidang' => 'AI',
            'nama_bidang' => 'Kecerdasan Buatan',
            'deskripsi' => 'Bidang ilmu komputer yang dikhususkan untuk memecahkan masalah kognitif yang umumnya terkait dengan kecerdasan manusia, seperti pembelajaran, pemecahan masalah, dan pengenalan pola.',
        ]);

        $jkk = MinatBidang::create([
            'kode_bidang' => 'JKK',
            'nama_bidang' => 'Jaringan Komputer & Keamanan',
            'deskripsi' => 'Fokus pada infrastruktur dan keamanan sistem komputer yang terhubung, memastikan komunikasi data yang efisien dan aman.',
        ]);

        $ds = MinatBidang::create([
            'kode_bidang' => 'DS',
            'nama_bidang' => 'Sains Data',
            'deskripsi' => 'Ilmu interdisipliner yang menggunakan metode, proses, algoritma, dan sistem ilmiah untuk mengekstrak pengetahuan dan wawasan dari banyak data terstruktur dan tidak terstruktur.',
        ]);

        // 2. Buat Area Riset (Sub-bidang Spesifik)

        // Area Riset di bawah RPL
        AreaRiset::create([
            'minat_bidang_id' => $rpl->id,
            'kode_area' => 'WEB_MOBILE',
            'nama_area' => 'Pengembangan Web dan Mobile',
            'deskripsi' => 'Fokus pada perancangan, pengembangan, dan pemeliharaan aplikasi yang berjalan di browser web atau pada perangkat seluler (Android/iOS).',
            'kata_kunci_teknologi' => 'HTML, CSS, JavaScript, React, Vue, Angular, Node.js, PHP, Laravel, Kotlin, Swift, Flutter, React Native',
            'kata_kunci_metode' => 'Responsive Design, Progressive Web Apps (PWA), API Integration, State Management, Agile, Scrum',
            'contoh_studi_kasus' => 'Sistem E-commerce, Aplikasi Media Sosial, Aplikasi Booking Online, Sistem Informasi Akademik',
        ]);
        AreaRiset::create([
            'minat_bidang_id' => $rpl->id,
            'kode_area' => 'GAME_DEV',
            'nama_area' => 'Pengembangan Game',
            'deskripsi' => 'Mencakup proses pembuatan video game, termasuk desain game, grafika komputer, fisika game, dan kecerdasan buatan untuk karakter non-pemain (NPC).',
            'kata_kunci_teknologi' => 'Unity, Unreal Engine, C#, C++, Blender, 3ds Max, OpenGL, DirectX',
            'kata_kunci_metode' => 'Game Loop, State Machine, Physics Engine, Pathfinding, Level Design',
            'contoh_studi_kasus' => 'Game Edukasi, Game Simulasi, Game 2D Platformer, Game 3D Adventure',
        ]);

        // Area Riset di bawah Kecerdasan Buatan
        AreaRiset::create([
            'minat_bidang_id' => $ai->id,
            'kode_area' => 'EXPERT_SYSTEM',
            'nama_area' => 'Sistem Pakar',
            'deskripsi' => 'Sistem yang meniru kemampuan pengambilan keputusan seorang ahli manusia dalam domain pengetahuan yang sempit.',
            'kata_kunci_teknologi' => 'Prolog, LISP, CLIPS, Python',
            'kata_kunci_metode' => 'Forward Chaining, Backward Chaining, Certainty Factor, Fuzzy Logic, Case-Based Reasoning',
            'contoh_studi_kasus' => 'Sistem Diagnosis Penyakit, Sistem Rekomendasi Pemupukan, Sistem Penasihat Keuangan',
        ]);
        AreaRiset::create([
            'minat_bidang_id' => $ai->id,
            'kode_area' => 'NLP',
            'nama_area' => 'Pemrosesan Bahasa Alami (NLP)',
            'deskripsi' => 'Cabang AI yang berfokus pada interaksi antara komputer dan bahasa manusia, memungkinkan mesin untuk membaca, memahami, dan menafsirkan ucapan atau teks.',
            'kata_kunci_teknologi' => 'Python, NLTK, SpaCy, TensorFlow, PyTorch, Transformer Models (BERT, GPT)',
            'kata_kunci_metode' => 'Sentiment Analysis, Text Classification, Named Entity Recognition (NER), Machine Translation, Topic Modeling',
            'contoh_studi_kasus' => 'Analisis Sentimen Ulasan Produk, Chatbot, Penerjemah Otomatis, Klasifikasi Berita',
        ]);

        // Area Riset di bawah Jaringan & Keamanan
        AreaRiset::create([
            'minat_bidang_id' => $jkk->id,
            'kode_area' => 'CYBER_SEC',
            'nama_area' => 'Keamanan Siber',
            'deskripsi' => 'Praktik melindungi sistem, jaringan, dan data dari serangan, kerusakan, atau akses tidak sah.',
            'kata_kunci_teknologi' => 'Firewall, IDS/IPS, SIEM, Wireshark, Metasploit, Kali Linux, Python, Bash Scripting',
            'kata_kunci_metode' => 'Penetration Testing, Digital Forensics, Cryptography, Network Security Analysis, Malware Analysis',
            'contoh_studi_kasus' => 'Analisis Forensik Serangan Jaringan, Pengembangan Sistem Deteksi Intrusi, Pengujian Keamanan Aplikasi Web',
        ]);

        // Area Riset di bawah Sains Data
        AreaRiset::create([
            'minat_bidang_id' => $ds->id,
            'kode_area' => 'DATA_ANALYTICS',
            'nama_area' => 'Analitika Data dan Business Intelligence',
            'deskripsi' => 'Proses memeriksa kumpulan data untuk menarik kesimpulan tentang informasi yang dikandungnya, seringkali untuk membuat keputusan bisnis yang lebih baik.',
            'kata_kunci_teknologi' => 'SQL, Python, R, Pandas, NumPy, Scikit-learn, Tableau, Power BI, Apache Spark',
            'kata_kunci_metode' => 'Predictive Modeling, Clustering, Classification, Regression, Data Visualization, A/B Testing',
            'contoh_studi_kasus' => 'Prediksi Churn Pelanggan, Segmentasi Pasar, Analisis Keranjang Belanja, Dashboard Performa Penjualan',
        ]);
    }
}
