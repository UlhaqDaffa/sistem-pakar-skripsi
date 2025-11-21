<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
        DB::table('pertanyaan_opsi_jawaban_template')->delete();
        Pertanyaan::truncate();
        OpsiJawabanTemplateItem::truncate();
        OpsiJawabanTemplate::truncate();

        $umum = KategoriPertanyaan::where('kode_kategori', 'UMUM')->firstOrFail();
        $minat = KategoriPertanyaan::where('kode_kategori', 'MINAT')->firstOrFail();
        $asesmen = KategoriPertanyaan::where('kode_kategori', 'ASESMEN')->firstOrFail();
        $discriminator = KategoriPertanyaan::where('kode_kategori', 'DISK')->firstOrFail();

        $templateLikert = $this->createLikertTemplate();
        $templateArketipe = $this->createArketipeTemplate();
        $templatesMinat = $this->createMinatTemplates();
        $this->createNilaiAETemplate();
        $templateDiscriminator = $this->createDiscriminatorTemplate();

        $startQuestion = Pertanyaan::create([
            'kategori_id' => $umum->id,
            'kode_pertanyaan' => 'ARKETIPE_01',
            'teks_pertanyaan' => 'Dari aktivitas berikut, manakah yang paling menggambarkan diri Anda atau paling Anda nikmati?',
            'is_start_point' => true,
        ]);
        $startQuestion->opsiJawabanTemplate()->attach($templateArketipe->id);

        $this->createMinatQuestions($minat, $templatesMinat);
        $this->createDiscriminatorQuestions($discriminator, $templateDiscriminator);
        $this->createAsesmenQuestions($asesmen, $templateLikert);
    }

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

    private function createMinatTemplates(): array
    {
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

    private function createMinatQuestions($kategoriMinat, array $templates): void
    {
        $questions = [
            'CREATOR' => 'Sebagai seorang "Creator", bidang apa yang paling menarik minat Anda untuk menciptakan sesuatu?',
            'ANALIS' => 'Sebagai seorang "Analis", jenis analisis atau data apa yang paling membuat Anda penasaran?',
            'ARCHITECT' => 'Sebagai seorang "Architect", arsitektur sistem di level mana yang paling ingin Anda rancang?',
        ];

        foreach ($questions as $archetype => $text) {
            $pertanyaan = Pertanyaan::create([
                'kategori_id' => $kategoriMinat->id,
                'kode_pertanyaan' => 'MINAT_' . $archetype . '_01',
                'teks_pertanyaan' => $text,
            ]);

            $pertanyaan->opsiJawabanTemplate()->attach($templates[$archetype]->id);
        }
    }

    private function createDiscriminatorTemplate(): OpsiJawabanTemplate
    {
        $template = OpsiJawabanTemplate::create([
            'kode_template' => 'DISK_ARKETIPE',
            'nama_template' => 'Discriminator Arketipe',
            'deskripsi' => 'Menentukan kecenderungan Creator, Analis, atau Architect pada minat terpilih.',
        ]);

        $opsi = [
            ['kode' => 'DISK_CREATOR', 'teks' => 'Saya ingin fokus pada eksperimen visual/prototyping dan membawa ide cepat menjadi produk nyata.', 'nilai' => 1],
            ['kode' => 'DISK_ANALIS', 'teks' => 'Saya ingin mengevaluasi data, metrik, atau resiko untuk memastikan keputusan berbasis bukti.', 'nilai' => 2],
            ['kode' => 'DISK_ARCHITECT', 'teks' => 'Saya ingin menyiapkan fondasi teknis, integrasi sistem, dan memastikan skalabilitas.', 'nilai' => 3],
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

    private function createDiscriminatorQuestions(KategoriPertanyaan $kategori, OpsiJawabanTemplate $template): void
    {
        $questions = [
            'RPL' => 'Saat mengerjakan proyek Rekayasa Perangkat Lunak, bagian mana yang paling Anda ingin kuasai?',
            'PENG' => 'Dalam pengembangan aplikasi end-to-end, fokus terdalam Anda ingin berada pada tahap apa?',
            'AI' => 'Ketika mengerjakan proyek AI modern, bagian mana yang paling membuat Anda antusias?',
            'DATA' => 'Saat mengubah data menjadi insight, Anda lebih ingin berperan sebagai apa?',
            'CITRA' => 'Dalam proyek Computer Vision, fokus Anda cenderung pada aspek apa?',
            'NLP' => 'Ketika merancang solusi NLP, Anda ingin memperkuat aspek apa terlebih dahulu?',
            'HCI' => 'Dalam proyek UI/UX & HCI, Anda ingin paling berperan pada bagian mana?',
            'GRAF' => 'Untuk proyek Grafika Komputer, Anda paling ingin terlibat di bagian apa?',
            'JAR' => 'Saat mengerjakan jaringan & keamanan siber, Anda cenderung mengambil peran apa?',
            'IOT' => 'Dalam solusi IoT end-to-end, Anda paling ingin fokus pada bagian apa?',
        ];

        foreach ($questions as $kodeMinat => $text) {
            $pertanyaan = Pertanyaan::create([
                'kategori_id' => $kategori->id,
                'kode_pertanyaan' => 'DISK_' . $kodeMinat . '_01',
                'teks_pertanyaan' => $text,
            ]);

            $pertanyaan->opsiJawabanTemplate()->attach($template->id);
        }
    }

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

        $asesmenQuestions = [
            'RPL' => [
                'Seberapa paham Anda tentang konsep Software Development Life Cycle (SDLC)?',
                'Seberapa familiar Anda dengan metodologi pengembangan perangkat lunak (Agile, Scrum, Waterfall)?',
                'Seberapa mahir Anda dalam menggunakan tools version control seperti Git?',
                'Seberapa paham Anda tentang konsep arsitektur perangkat lunak (MVC, Microservices, dll)?',
                'Seberapa berpengalaman Anda dalam melakukan testing perangkat lunak (Unit Testing, Integration Testing)?',
            ],
            'PENG' => [
                'Seberapa paham Anda tentang dasar-dasar pemrograman (variabel, fungsi, struktur data)?',
                'Seberapa mahir Anda dalam menggunakan framework pengembangan aplikasi (Laravel, React, Flutter, dll)?',
                'Seberapa familiar Anda dengan konsep REST API dan integrasi backend-frontend?',
                'Seberapa berpengalaman Anda dalam mengembangkan aplikasi full-stack?',
                'Seberapa paham Anda tentang database design dan query optimization?',
            ],
            'AI' => [
                'Seberapa kuat pemahaman Anda tentang konsep dasar Machine Learning?',
                'Seberapa mahir Anda menggunakan library Python untuk AI (TensorFlow, PyTorch, Scikit-learn)?',
                'Seberapa familiar Anda dengan algoritma Machine Learning (Neural Network, Decision Tree, SVM)?',
                'Seberapa berpengalaman Anda dalam membangun model prediksi atau klasifikasi?',
                'Seberapa paham Anda tentang konsep Deep Learning dan Neural Networks?',
            ],
            'DATA' => [
                'Seberapa kuat pemahaman Anda tentang statistika dan probabilitas?',
                'Seberapa mahir Anda menggunakan Python untuk analisis data (Pandas, NumPy, Matplotlib)?',
                'Seberapa familiar Anda dengan konsep Data Mining dan preprocessing data?',
                'Seberapa berpengalaman Anda dalam membangun sistem rekomendasi atau analisis sentimen?',
                'Seberapa paham Anda tentang Big Data technologies (Hadoop, Spark, dll)?',
            ],
            'CITRA' => [
                'Seberapa paham Anda tentang dasar-dasar pemrosesan citra digital?',
                'Seberapa mahir Anda menggunakan library OpenCV untuk manipulasi gambar?',
                'Seberapa familiar Anda dengan konsep Computer Vision dan Object Detection?',
                'Seberapa berpengalaman Anda dalam menggunakan Deep Learning untuk image classification?',
                'Seberapa paham Anda tentang algoritma pengenalan pola dalam citra?',
            ],
            'NLP' => [
                'Seberapa paham Anda tentang dasar-dasar Natural Language Processing?',
                'Seberapa mahir Anda menggunakan library NLP (NLTK, spaCy, Transformers)?',
                'Seberapa familiar Anda dengan konsep Text Mining dan Sentiment Analysis?',
                'Seberapa berpengalaman Anda dalam membangun chatbot atau sistem terjemahan?',
                'Seberapa paham Anda tentang model bahasa modern (BERT, GPT, dll)?',
            ],
            'HCI' => [
                'Seberapa paham Anda tentang prinsip-prinsip desain UI/UX?',
                'Seberapa mahir Anda menggunakan tools desain (Figma, Adobe XD, Sketch)?',
                'Seberapa familiar Anda dengan konsep Usability Testing dan User Research?',
                'Seberapa berpengalaman Anda dalam membuat wireframe dan prototype?',
                'Seberapa paham Anda tentang prinsip aksesibilitas dalam desain antarmuka?',
            ],
            'GRAF' => [
                'Seberapa paham Anda tentang dasar-dasar grafika komputer 2D dan 3D?',
                'Seberapa mahir Anda menggunakan game engine (Unity, Unreal Engine, Godot)?',
                'Seberapa familiar Anda dengan konsep rendering dan shader programming?',
                'Seberapa berpengalaman Anda dalam membuat animasi atau visual effects?',
                'Seberapa paham Anda tentang konsep Virtual Reality (VR) atau Augmented Reality (AR)?',
            ],
            'JAR' => [
                'Seberapa paham Anda tentang dasar-dasar jaringan komputer dan protokol (TCP/IP, HTTP)?',
                'Seberapa mahir Anda dalam mengkonfigurasi jaringan dan troubleshooting?',
                'Seberapa familiar Anda dengan konsep keamanan siber dan serangan umum?',
                'Seberapa berpengalaman Anda dalam melakukan penetration testing atau security audit?',
                'Seberapa paham Anda tentang kriptografi dan enkripsi data?',
            ],
            'IOT' => [
                'Seberapa paham Anda tentang dasar-dasar elektronika dan mikrokontroler?',
                'Seberapa mahir Anda menggunakan platform IoT (Arduino, Raspberry Pi, ESP32)?',
                'Seberapa familiar Anda dengan protokol komunikasi IoT (MQTT, HTTP, LoRaWAN)?',
                'Seberapa berpengalaman Anda dalam membangun sistem monitoring berbasis sensor?',
                'Seberapa paham Anda tentang konsep Smart Home atau Smart City?',
            ],
        ];

        foreach ($asesmenQuestions as $kodeMinat => $questions) {
            foreach ($questions as $index => $question) {
                $kode = 'ASESMEN_' . $kodeMinat . '_' . str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                $createQuestion($kode, $question);
            }
        }
    }
}
