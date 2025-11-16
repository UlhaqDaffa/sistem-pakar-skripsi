<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriPertanyaan;
use App\Models\Pertanyaan;
use App\Models\OpsiJawabanTemplate;
use App\Models\OpsiJawabanTemplateItem;

class PertanyaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data existing
        \DB::table('pertanyaan_opsi_jawaban_template')->delete();
        Pertanyaan::truncate();
        OpsiJawabanTemplateItem::truncate();
        OpsiJawabanTemplate::truncate();

        // Get Kategori IDs
        $umum = KategoriPertanyaan::where('kode_kategori', 'UMUM')->firstOrFail();
        $minat = KategoriPertanyaan::where('kode_kategori', 'MINAT')->firstOrFail();
        $asesmen = KategoriPertanyaan::where('kode_kategori', 'ASESMEN')->firstOrFail();

        // === BUAT TEMPLATE OPSI JAWABAN ===
        $templateLikert = $this->createLikertTemplate();
        $templateArketipe = $this->createArketipeTemplate();
        
        // Template minat untuk semua 10 minat bidang
        $templatesMinat = $this->createMinatTemplates();
        
        // Template untuk nilai A-E (untuk input nilai mata kuliah)
        $templateNilaiAE = $this->createNilaiAETemplate();

        // === TAHAP 1: PERTANYAAN UMUM (ARKETIPE) ===
        $p1 = Pertanyaan::create([
            'kategori_id' => $umum->id,
            'kode_pertanyaan' => 'ARKETIPE_01',
            'teks_pertanyaan' => 'Dari aktivitas berikut, manakah yang paling menggambarkan diri Anda atau paling Anda nikmati?',
            'is_start_point' => true,
        ]);
        $p1->opsiJawabanTemplate()->attach($templateArketipe->id);

        // === TAHAP 2: PERTANYAAN MINAT (BERDASARKAN ARKETIPE) ===
        $this->createMinatQuestions($minat, $templatesMinat);

        // === TAHAP 3: PERTANYAAN ASESMEN (BERDASARKAN MINAT) ===
        $this->createAsesmenQuestions($asesmen, $templateLikert);
    }

    /**
     * Buat template Likert 1-5
     */
    private function createLikertTemplate(): OpsiJawabanTemplate
    {
        $template = OpsiJawabanTemplate::create([
            'kode_template' => 'LIKERT_1_5',
            'nama_template' => 'Skala Likert 1-5',
            'deskripsi' => 'Skala Likert untuk mengukur tingkat pemahaman (1-5)',
        ]);

        $skala = [
            1 => 'Sangat Tidak Paham',
            2 => 'Tidak Paham',
            3 => 'Cukup Paham',
            4 => 'Paham',
            5 => 'Sangat Paham',
        ];

        foreach ($skala as $nilai => $teks) {
            OpsiJawabanTemplateItem::create([
                'template_id' => $template->id,
                'kode_jawaban' => 'SKALA_' . $nilai,
                'teks_jawaban' => $teks,
                'nilai' => $nilai,
                'urutan' => $nilai - 1,
            ]);
        }

        return $template;
    }

    /**
     * Buat template Arketipe
     */
    private function createArketipeTemplate(): OpsiJawabanTemplate
    {
        $template = OpsiJawabanTemplate::create([
            'kode_template' => 'ARKETIPE_UMUM',
            'nama_template' => 'Opsi Arketipe',
            'deskripsi' => 'Opsi untuk memilih arketipe (Creator, Analis, Architect)',
        ]);

        $opsi = [
            ['kode' => 'ARKETIPE_CREATOR', 'teks' => 'Saya suka merancang dan membangun sesuatu yang baru dari awal, seperti membuat aplikasi, website, atau karya visual.', 'nilai' => 1],
            ['kode' => 'ARKETIPE_ANALIS', 'teks' => 'Saya menikmati proses menganalisis informasi, menemukan pola tersembunyi, dan memecahkan masalah yang rumit.', 'nilai' => 2],
            ['kode' => 'ARKETIPE_ARCHITECT', 'teks' => 'Saya tertarik dalam merancang sistem yang besar dan kompleks, memastikan semua komponen dapat terhubung dan bekerja secara efisien.', 'nilai' => 3],
        ];

        foreach ($opsi as $index => $item) {
            OpsiJawabanTemplateItem::create([
                'template_id' => $template->id,
                'kode_jawaban' => $item['kode'],
                'teks_jawaban' => $item['teks'],
                'nilai' => $item['nilai'],
                'urutan' => $index,
            ]);
        }

        return $template;
    }

    /**
     * Buat template nilai A-E untuk input nilai mata kuliah
     */
    private function createNilaiAETemplate(): OpsiJawabanTemplate
    {
        $template = OpsiJawabanTemplate::create([
            'kode_template' => 'NILAI_A_E',
            'nama_template' => 'Skala Nilai A-E',
            'deskripsi' => 'Skala nilai mata kuliah A-E (A=4, B=3, C=2, D=1, E=0)',
        ]);

        $skala = [
            ['kode' => 'NILAI_A', 'teks' => 'A (Sangat Baik)', 'nilai' => 4],
            ['kode' => 'NILAI_B', 'teks' => 'B (Baik)', 'nilai' => 3],
            ['kode' => 'NILAI_C', 'teks' => 'C (Cukup)', 'nilai' => 2],
            ['kode' => 'NILAI_D', 'teks' => 'D (Kurang)', 'nilai' => 1],
            ['kode' => 'NILAI_E', 'teks' => 'E (Sangat Kurang)', 'nilai' => 0],
        ];

        foreach ($skala as $index => $item) {
            OpsiJawabanTemplateItem::create([
                'template_id' => $template->id,
                'kode_jawaban' => $item['kode'],
                'teks_jawaban' => $item['teks'],
                'nilai' => $item['nilai'],
                'urutan' => $index,
            ]);
        }

        return $template;
    }

    /**
     * Buat template minat untuk semua 10 minat bidang
     */
    private function createMinatTemplates(): array
    {
        // Mapping minat bidang ke opsi minat
        $minatMappings = [
            'CREATOR' => [
                ['kode' => 'MINAT_RPL', 'teks' => 'Rekayasa Perangkat Lunak (Software Engineering)', 'nilai' => 1],
                ['kode' => 'MINAT_PENG', 'teks' => 'Pengembangan Aplikasi (Application Development)', 'nilai' => 2],
                ['kode' => 'MINAT_GRAF', 'teks' => 'Grafika Komputer & Multimedia', 'nilai' => 3],
            ],
            'ANALIS' => [
                ['kode' => 'MINAT_AI', 'teks' => 'Kecerdasan Buatan (Artificial Intelligence)', 'nilai' => 1],
                ['kode' => 'MINAT_DATA', 'teks' => 'Sains Data & Big Data', 'nilai' => 2],
                ['kode' => 'MINAT_CITRA', 'teks' => 'Pemrosesan Citra & Visi Komputer', 'nilai' => 3],
                ['kode' => 'MINAT_NLP', 'teks' => 'Pemrosesan Bahasa Alami (NLP)', 'nilai' => 4],
            ],
            'ARCHITECT' => [
                ['kode' => 'MINAT_HCI', 'teks' => 'Desain UI/UX & Interaksi Manusia-Komputer', 'nilai' => 1],
                ['kode' => 'MINAT_JAR', 'teks' => 'Jaringan & Keamanan Siber', 'nilai' => 2],
                ['kode' => 'MINAT_IOT', 'teks' => 'Sistem Tertanam & Internet of Things', 'nilai' => 3],
            ],
        ];

        $templates = [];

        foreach ($minatMappings as $archetype => $opsi) {
            $template = OpsiJawabanTemplate::create([
                'kode_template' => 'MINAT_' . $archetype,
                'nama_template' => 'Opsi Minat ' . $archetype,
                'deskripsi' => 'Opsi untuk minat bidang ' . $archetype,
            ]);

            foreach ($opsi as $index => $item) {
                OpsiJawabanTemplateItem::create([
                    'template_id' => $template->id,
                    'kode_jawaban' => $item['kode'],
                    'teks_jawaban' => $item['teks'],
                    'nilai' => $item['nilai'],
                    'urutan' => $index,
                ]);
            }

            $templates[$archetype] = $template;
        }

        return $templates;
    }

    /**
     * Buat pertanyaan minat berdasarkan arketipe
     */
    private function createMinatQuestions($kategoriMinat, array $templates): void
    {
        // Pertanyaan untuk Creator
        $p1 = Pertanyaan::create([
            'kategori_id' => $kategoriMinat->id,
            'kode_pertanyaan' => 'MINAT_CREATOR_01',
            'teks_pertanyaan' => 'Sebagai seorang "Creator", bidang apa yang paling menarik minat Anda untuk menciptakan sesuatu?',
        ]);
        $p1->opsiJawabanTemplate()->attach($templates['CREATOR']->id);

        // Pertanyaan untuk Analis
        $p2 = Pertanyaan::create([
            'kategori_id' => $kategoriMinat->id,
            'kode_pertanyaan' => 'MINAT_ANALIS_01',
            'teks_pertanyaan' => 'Sebagai seorang "Analis", jenis analisis atau data apa yang paling membuat Anda penasaran?',
        ]);
        $p2->opsiJawabanTemplate()->attach($templates['ANALIS']->id);

        // Pertanyaan untuk Architect
        $p3 = Pertanyaan::create([
            'kategori_id' => $kategoriMinat->id,
            'kode_pertanyaan' => 'MINAT_ARCHITECT_01',
            'teks_pertanyaan' => 'Sebagai seorang "Architect", arsitektur sistem di level mana yang paling ingin Anda rancang?',
        ]);
        $p3->opsiJawabanTemplate()->attach($templates['ARCHITECT']->id);
    }

    /**
     * Buat pertanyaan asesmen untuk semua minat bidang
     */
    private function createAsesmenQuestions($kategoriAsesmen, $templateLikert): void
    {
        $createQuestion = function ($kode, $teks) use ($kategoriAsesmen, $templateLikert) {
            $pertanyaan = Pertanyaan::create([
                'kategori_id' => $kategoriAsesmen->id,
                'kode_pertanyaan' => $kode,
                'teks_pertanyaan' => $teks,
            ]);
            $pertanyaan->opsiJawabanTemplate()->attach($templateLikert->id);
        };

        // Mapping kode minat ke pertanyaan asesmen
        $asesmenQuestions = [
            // RPL - Rekayasa Perangkat Lunak
            'RPL' => [
                'Seberapa paham Anda tentang konsep Software Development Life Cycle (SDLC)?',
                'Seberapa familiar Anda dengan metodologi pengembangan perangkat lunak (Agile, Scrum, Waterfall)?',
                'Seberapa mahir Anda dalam menggunakan tools version control seperti Git?',
                'Seberapa paham Anda tentang konsep arsitektur perangkat lunak (MVC, Microservices, dll)?',
                'Seberapa berpengalaman Anda dalam melakukan testing perangkat lunak (Unit Testing, Integration Testing)?',
            ],
            // PENG - Pengembangan Aplikasi
            'PENG' => [
                'Seberapa paham Anda tentang dasar-dasar pemrograman (variabel, fungsi, struktur data)?',
                'Seberapa mahir Anda dalam menggunakan framework pengembangan aplikasi (Laravel, React, Flutter, dll)?',
                'Seberapa familiar Anda dengan konsep REST API dan integrasi backend-frontend?',
                'Seberapa berpengalaman Anda dalam mengembangkan aplikasi full-stack?',
                'Seberapa paham Anda tentang database design dan query optimization?',
            ],
            // AI - Kecerdasan Buatan
            'AI' => [
                'Seberapa kuat pemahaman Anda tentang konsep dasar Machine Learning?',
                'Seberapa mahir Anda menggunakan library Python untuk AI (TensorFlow, PyTorch, Scikit-learn)?',
                'Seberapa familiar Anda dengan algoritma Machine Learning (Neural Network, Decision Tree, SVM)?',
                'Seberapa berpengalaman Anda dalam membangun model prediksi atau klasifikasi?',
                'Seberapa paham Anda tentang konsep Deep Learning dan Neural Networks?',
            ],
            // DATA - Sains Data & Big Data
            'DATA' => [
                'Seberapa kuat pemahaman Anda tentang statistika dan probabilitas?',
                'Seberapa mahir Anda menggunakan Python untuk analisis data (Pandas, NumPy, Matplotlib)?',
                'Seberapa familiar Anda dengan konsep Data Mining dan preprocessing data?',
                'Seberapa berpengalaman Anda dalam membangun sistem rekomendasi atau analisis sentimen?',
                'Seberapa paham Anda tentang Big Data technologies (Hadoop, Spark, dll)?',
            ],
            // CITRA - Pemrosesan Citra & Visi Komputer
            'CITRA' => [
                'Seberapa paham Anda tentang dasar-dasar pemrosesan citra digital?',
                'Seberapa mahir Anda menggunakan library OpenCV untuk manipulasi gambar?',
                'Seberapa familiar Anda dengan konsep Computer Vision dan Object Detection?',
                'Seberapa berpengalaman Anda dalam menggunakan Deep Learning untuk image classification?',
                'Seberapa paham Anda tentang algoritma pengenalan pola dalam citra?',
            ],
            // NLP - Pemrosesan Bahasa Alami
            'NLP' => [
                'Seberapa paham Anda tentang dasar-dasar Natural Language Processing?',
                'Seberapa mahir Anda menggunakan library NLP (NLTK, spaCy, Transformers)?',
                'Seberapa familiar Anda dengan konsep Text Mining dan Sentiment Analysis?',
                'Seberapa berpengalaman Anda dalam membangun chatbot atau sistem terjemahan?',
                'Seberapa paham Anda tentang model bahasa modern (BERT, GPT, dll)?',
            ],
            // HCI - Desain UI/UX & Interaksi Manusia-Komputer
            'HCI' => [
                'Seberapa paham Anda tentang prinsip-prinsip desain UI/UX?',
                'Seberapa mahir Anda menggunakan tools desain (Figma, Adobe XD, Sketch)?',
                'Seberapa familiar Anda dengan konsep Usability Testing dan User Research?',
                'Seberapa berpengalaman Anda dalam membuat wireframe dan prototype?',
                'Seberapa paham Anda tentang prinsip aksesibilitas dalam desain antarmuka?',
            ],
            // GRAF - Grafika Komputer & Multimedia
            'GRAF' => [
                'Seberapa paham Anda tentang dasar-dasar grafika komputer 2D dan 3D?',
                'Seberapa mahir Anda menggunakan game engine (Unity, Unreal Engine, Godot)?',
                'Seberapa familiar Anda dengan konsep rendering dan shader programming?',
                'Seberapa berpengalaman Anda dalam membuat animasi atau visual effects?',
                'Seberapa paham Anda tentang konsep Virtual Reality (VR) atau Augmented Reality (AR)?',
            ],
            // JAR - Jaringan & Keamanan Siber
            'JAR' => [
                'Seberapa paham Anda tentang dasar-dasar jaringan komputer dan protokol (TCP/IP, HTTP)?',
                'Seberapa mahir Anda dalam mengkonfigurasi jaringan dan troubleshooting?',
                'Seberapa familiar Anda dengan konsep keamanan siber dan serangan umum?',
                'Seberapa berpengalaman Anda dalam melakukan penetration testing atau security audit?',
                'Seberapa paham Anda tentang kriptografi dan enkripsi data?',
            ],
            // IOT - Sistem Tertanam & Internet of Things
            'IOT' => [
                'Seberapa paham Anda tentang dasar-dasar elektronika dan mikrokontroler?',
                'Seberapa mahir Anda menggunakan platform IoT (Arduino, Raspberry Pi, ESP32)?',
                'Seberapa familiar Anda dengan protokol komunikasi IoT (MQTT, HTTP, LoRaWAN)?',
                'Seberapa berpengalaman Anda dalam membangun sistem monitoring berbasis sensor?',
                'Seberapa paham Anda tentang konsep Smart Home atau Smart City?',
            ],
        ];

        // Buat pertanyaan asesmen untuk setiap minat bidang
        foreach ($asesmenQuestions as $kodeMinat => $questions) {
            foreach ($questions as $index => $question) {
                $kode = 'ASESMEN_' . $kodeMinat . '_' . str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                $createQuestion($kode, $question);
            }
        }
    }
}
