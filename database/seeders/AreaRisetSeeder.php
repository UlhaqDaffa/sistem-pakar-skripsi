<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\AreaRiset;
use App\Models\MinatBidang;
use App\Models\Tag;

class AreaRisetSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('area_riset_tags')->truncate();
        DB::table('area_riset_minat_bidang')->truncate();
        Tag::truncate();
        AreaRiset::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $minatBidangs = MinatBidang::all()->keyBy('kode_bidang');
        $dataset = $this->dataset();

        foreach ($dataset as $kodeMinat => $areas) {
            $primaryMinat = $minatBidangs[$kodeMinat] ?? null;

            if (!$primaryMinat) {
                continue;
            }

            foreach ($areas as $areaData) {
                $area = AreaRiset::create([
                    'kode_area' => $areaData['kode'],
                    'nama_area' => $areaData['nama'],
                    'deskripsi' => $areaData['deskripsi'],
                    'contoh_studi_kasus' => $areaData['studi'],
                    'tujuan_masalah' => $areaData['tujuan'],
                    'tipe_sistem' => $areaData['tipe'],
                    'target_arketipe' => $areaData['target'],
                    'level_kesulitan' => $areaData['level'],
                ]);

                $area->minatBidangs()->attach($primaryMinat->id);

                foreach ($areaData['relasi_minat'] ?? [] as $relasiKode) {
                    $relasi = $minatBidangs[$relasiKode] ?? null;
                    if ($relasi) {
                        $area->minatBidangs()->attach($relasi->id);
                    }
                }

                $this->syncTags($area, $areaData['tags'] ?? []);
            }
        }
    }

    private function syncTags(AreaRiset $area, array $tagGroups): void
    {
        $tagIds = [];

        foreach (['teknologi' => 'TEKNOLOGI', 'metode' => 'METODE'] as $key => $type) {
            foreach ($tagGroups[$key] ?? [] as $tagName) {
                $name = trim($tagName);
                if ($name === '') {
                    continue;
                }

                $tag = Tag::firstOrCreate([
                    'nama_tag' => $name,
                    'tipe' => $type,
                ]);

                $tagIds[] = $tag->id;
            }
        }

        if (!empty($tagIds)) {
            $area->tags()->syncWithoutDetaching($tagIds);
        }
    }

    private function dataset(): array
    {
        return [
            'RPL' => [
                [
                    'kode' => 'RPL-01',
                    'nama' => 'Arsitektur Microservices Resilien Berbasis Observability',
                    'deskripsi' => 'Merancang platform microservices dengan tracing end-to-end, resiliency pattern, dan pembelajaran dari chaos experiment agar layanan e-commerce tahan lonjakan traffic 2025.',
                    'tujuan' => 'Meningkatkan reliability layanan digital melalui observability dan otomatisasi perbaikan.',
                    'tipe' => 'Platform Observability & SRE Dashboard',
                    'target' => 'ARCHITECT',
                    'level' => 3,
                    'studi' => 'Migrasi modul pembayaran B2B menjadi layanan terpisah, Stress test flash-sale menggunakan chaos engineering',
                    'tags' => [
                        'teknologi' => ['Kubernetes', 'Istio', 'OpenTelemetry', 'Grafana Tempo', 'Kafka'],
                        'metode' => ['Domain-Driven Design', 'Chaos Engineering', 'Circuit Breaker', 'SAGA Pattern'],
                    ],
                ],
                [
                    'kode' => 'RPL-02',
                    'nama' => 'DevSecOps Intelligent Pipeline dengan Policy-as-Code',
                    'deskripsi' => 'Mengintegrasikan scanning keamanan, compliance, dan AI code reviewer langsung di pipeline CI/CD agar rilis harian tetap aman.',
                    'tujuan' => 'Mengurangi kerentanan perangkat lunak sejak tahap build melalui otomatisasi DevSecOps.',
                    'tipe' => 'Pipeline Observability & Policy-as-Code Service',
                    'target' => 'ARCHITECT',
                    'level' => 2,
                    'studi' => 'Integrasi Trivy dan OPA di GitLab CI, Dashboard compliance real-time untuk startup fintech',
                    'tags' => [
                        'teknologi' => ['GitHub Actions', 'GitLab CI', 'Trivy', 'SonarQube', 'Open Policy Agent'],
                        'metode' => ['DevSecOps', 'Shift-left Security', 'Policy as Code', 'Continuous Compliance'],
                    ],
                ],
                [
                    'kode' => 'RPL-03',
                    'nama' => 'Green Software Engineering Metrics Platform',
                    'deskripsi' => 'Membangun modul analitik jejak karbon aplikasi dengan memantau konsumsi energi kode dan workload cloud.',
                    'tujuan' => 'Mendorong pengembangan perangkat lunak hemat energi dengan insight berbasis data.',
                    'tipe' => 'Dashboard GreenOps & Sustainability Analytics',
                    'target' => 'ANALIS',
                    'level' => 2,
                    'studi' => 'Benchmark emisi microservices vs monolith, Rekomendasi jadwal deployment hemat energi',
                    'tags' => [
                        'teknologi' => ['Carbon Aware SDK', 'PowerBI', 'Laravel', 'PostgreSQL'],
                        'metode' => ['GreenOps', 'Energy-aware Profiling', 'Digital Sustainability Index'],
                    ],
                ],
                [
                    'kode' => 'RPL-04',
                    'nama' => 'Software Supply Chain Security Control Tower',
                    'deskripsi' => 'Memonitor material bill of software (SBOM) serta reputasi dependensi untuk mencegah serangan supply chain.',
                    'tujuan' => 'Menghadirkan visibilitas penuh terhadap dependensi kritis melalui scoring risiko otomatis.',
                    'tipe' => 'Supply Chain Security Dashboard',
                    'target' => 'ARCHITECT',
                    'level' => 3,
                    'studi' => 'Audit SBOM untuk aplikasi kesehatan digital, Integrasi tanda tangan Sigstore ke proses rilis',
                    'tags' => [
                        'teknologi' => ['Sigstore', 'SLSA', 'Harbor', 'CycloneDX'],
                        'metode' => ['SBOM Analysis', 'Risk Scoring', 'Threat Modeling'],
                    ],
                ],
                [
                    'kode' => 'RPL-05',
                    'nama' => 'AI-assisted QA Automation Studio',
                    'deskripsi' => 'Menggabungkan generative AI untuk membuat test case dan self-healing locator pada suite otomatisasi.',
                    'tujuan' => 'Mempercepat kualitas rilis dengan pengujian adaptif dan rekomendasi coverage.',
                    'tipe' => 'Quality Intelligence & Test Automation Platform',
                    'target' => 'CREATOR',
                    'level' => 2,
                    'studi' => 'Auto generate scenario regresi aplikasi bank digital, Self-healing test untuk aplikasi super-app',
                    'tags' => [
                        'teknologi' => ['Playwright', 'LangChain', 'OpenAI API', 'Docker'],
                        'metode' => ['Generative Testing', 'Test Impact Analysis', 'Self-healing Automation'],
                    ],
                ],
                [
                    'kode' => 'RPL-06',
                    'nama' => 'Low-code Governance Toolkit',
                    'deskripsi' => 'Menentukan aturan, komponen reusable, dan audit trail untuk aplikasi low-code/no-code di enterprise.',
                    'tujuan' => 'Menjaga konsistensi desain serta keamanan solusi low-code buatan citizen developer.',
                    'tipe' => 'Governance Portal & Component Library',
                    'target' => 'GENERAL',
                    'level' => 1,
                    'studi' => 'Template approval workflow untuk dashboard HR, Library UI konsisten bagi citizen developer',
                    'tags' => [
                        'teknologi' => ['Budibase', 'Retool', 'Laravel', 'MySQL'],
                        'metode' => ['Component Governance', 'Design System', 'GRC Checklist'],
                    ],
                ],
            ],
            'PENG' => [
                [
                    'kode' => 'PENG-01',
                    'nama' => 'SuperApp Modular Frontend Platform',
                    'deskripsi' => 'Membangun arsitektur micro-frontend dengan desain sistem tunggal agar fitur baru dapat dilepas pasang tanpa downtime.',
                    'tujuan' => 'Mempercepat delivery fitur multi-tim di aplikasi superapp.',
                    'tipe' => 'Modular Frontend Orchestrator',
                    'target' => 'CREATOR',
                    'level' => 2,
                    'studi' => 'Portal layanan publik multi modul, Superapp logistik dengan bundling fitur lokal',
                    'tags' => [
                        'teknologi' => ['React', 'Module Federation', 'Tailwind', 'Nx'],
                        'metode' => ['Design System Tokens', 'Micro Frontend', 'Feature Flagging'],
                    ],
                ],
                [
                    'kode' => 'PENG-02',
                    'nama' => 'Offline-first Retail POS dengan Edge Sync',
                    'deskripsi' => 'Menghadirkan POS mobile yang tetap berfungsi saat koneksi terbatas dan melakukan sinkronisasi konflik otomatis.',
                    'tujuan' => 'Menjamin transaksi tetap tercatat di daerah dengan internet tidak stabil.',
                    'tipe' => 'Offline-first POS Platform',
                    'target' => 'ARCHITECT',
                    'level' => 3,
                    'studi' => 'Aplikasi kasir pasar modern dengan Cloudflare Workers, Sinkronisasi stok franchise makanan',
                    'tags' => [
                        'teknologi' => ['Flutter', 'SQLite', 'CouchDB', 'Cloudflare Workers'],
                        'metode' => ['Offline-first', 'Conflict Resolution', 'Edge Synchronization'],
                    ],
                ],
                [
                    'kode' => 'PENG-03',
                    'nama' => 'Generative UI Builder untuk UKM',
                    'deskripsi' => 'Menyediakan studio pembuatan antarmuka otomatis dari prompt teks yang langsung memproduksi komponen siap pakai.',
                    'tujuan' => 'Mengurangi waktu desain untuk bisnis kecil yang ingin go-digital.',
                    'tipe' => 'Generative UI Studio',
                    'target' => 'CREATOR',
                    'level' => 2,
                    'studi' => 'Generator dashboard toko online berbasis prompt Bahasa Indonesia, Auto layout admin panel untuk koperasi',
                    'tags' => [
                        'teknologi' => ['Next.js', 'Figma API', 'Tailwind', 'GenAI'],
                        'metode' => ['Prompt Engineering', 'Component Suggestion', 'Design Tokens'],
                    ],
                ],
                [
                    'kode' => 'PENG-04',
                    'nama' => 'PWA Accessibility & Performance Suite',
                    'deskripsi' => 'Toolkit untuk memastikan PWA siap audit WCAG 2.2 sekaligus optimal di perangkat low-end.',
                    'tujuan' => 'Memastikan aplikasi web setara bagi semua pengguna tanpa mengorbankan kecepatan.',
                    'tipe' => 'Progressive Web Testing Suite',
                    'target' => 'ANALIS',
                    'level' => 1,
                    'studi' => 'Audit platform belajar daring untuk screen reader, Optimasi e-commerce PWA di jaringan 3G',
                    'tags' => [
                        'teknologi' => ['Lighthouse', 'Storybook', 'Workbox'],
                        'metode' => ['Accessibility Audit', 'Performance Budget', 'User Journey Mapping'],
                    ],
                ],
                [
                    'kode' => 'PENG-05',
                    'nama' => 'Composable Commerce Starter Kit',
                    'deskripsi' => 'Blueprint toko online modern dengan pendekatan headless & BFF sehingga bisnis dapat merakit stack sesuai kebutuhan.',
                    'tujuan' => 'Memberi fleksibilitas integrasi layanan pihak ketiga (payment, OMS, loyalty).',
                    'tipe' => 'Composable Commerce Reference Architecture',
                    'target' => 'ARCHITECT',
                    'level' => 2,
                    'studi' => 'Marketplace niche dengan BFF GraphQL, Integrasi modul loyalty dan live shopping',
                    'tags' => [
                        'teknologi' => ['Shopify Hydrogen', 'MedusaJS', 'GraphQL', 'Stripe'],
                        'metode' => ['Composable Commerce', 'Backend for Frontend', 'API Orchestration'],
                    ],
                ],
            ],
            'AI' => [
                [
                    'kode' => 'AI-01',
                    'nama' => 'Explainable Credit Risk Scoring (XAI)',
                    'deskripsi' => 'Membangun model kredit modern dengan interpretabilitas lokal agar keputusan pinjaman dapat dijelaskan ke regulator.',
                    'tujuan' => 'Mengurangi bias dan meningkatkan kepercayaan terhadap sistem AI pada sektor finansial.',
                    'tipe' => 'Explainable AI Service Layer',
                    'target' => 'ANALIS',
                    'level' => 3,
                    'studi' => 'Penjelasan skor koperasi digital, Dashboard fairness untuk BNPL',
                    'tags' => [
                        'teknologi' => ['Python', 'SHAP', 'TensorFlow', 'MLflow'],
                        'metode' => ['Explainable AI', 'Fairness Metric', 'Model Monitoring'],
                    ],
                    'relasi_minat' => ['DATA'],
                ],
                [
                    'kode' => 'AI-02',
                    'nama' => 'Generative AI for Localized Content Studio',
                    'deskripsi' => 'Menggunakan LLM lokal serta retrieval augmented generation untuk membuat konten pemasaran multi bahasa daerah.',
                    'tujuan' => 'Mempercepat produksi konten yang sesuai budaya dan regulasi lokal.',
                    'tipe' => 'RAG Content Automation Platform',
                    'target' => 'CREATOR',
                    'level' => 2,
                    'studi' => 'Asisten copywriting wisata daerah, Generator konten kampus dalam bahasa daerah',
                    'tags' => [
                        'teknologi' => ['LangChain', 'OpenAI', 'LlamaIndex', 'Pinecone'],
                        'metode' => ['Retrieval Augmented Generation', 'Prompt Chaining', 'Guardrails'],
                    ],
                ],
                [
                    'kode' => 'AI-03',
                    'nama' => 'Responsible AI Monitoring Platform',
                    'deskripsi' => 'Menjaga lifecycle model dari sisi drift, audit, dan persetujuan etis sebelum deployment.',
                    'tujuan' => 'Menyediakan governance AI yang dapat diaudit.',
                    'tipe' => 'Responsible AI Operations Hub',
                    'target' => 'ARCHITECT',
                    'level' => 3,
                    'studi' => 'Approval workflow AI HRIS, Audit trail rekomendasi medis berbasis AI',
                    'tags' => [
                        'teknologi' => ['Weights & Biases', 'EvidentlyAI', 'Great Expectations'],
                        'metode' => ['Model Risk Management', 'Bias Detection', 'AI Governance'],
                    ],
                ],
                [
                    'kode' => 'AI-04',
                    'nama' => 'TinyML Edge Predictive Maintenance',
                    'deskripsi' => 'Mengemas model ML ringan ke microcontroller untuk memantau vibrasi mesin tanpa koneksi konstan.',
                    'tujuan' => 'Menurunkan downtime pabrik melalui deteksi dini di edge.',
                    'tipe' => 'Edge AI Maintenance Kit',
                    'target' => 'ARCHITECT',
                    'level' => 2,
                    'studi' => 'Monitoring motor listrik pabrik tekstil, Sensor pintu tol cerdas',
                    'tags' => [
                        'teknologi' => ['TensorFlow Lite', 'Edge Impulse', 'ESP32'],
                        'metode' => ['TinyML', 'Signal Processing', 'Edge Deployment'],
                    ],
                    'relasi_minat' => ['IOT'],
                ],
                [
                    'kode' => 'AI-05',
                    'nama' => 'Multi-Agent AI Ops Assistant',
                    'deskripsi' => 'Menata beberapa agen AI (diagnostik, remediation, knowledge) untuk membantu tim operasi TI.',
                    'tujuan' => 'Mengotomatisasi respon insiden dengan reasoning kolaboratif antar agen.',
                    'tipe' => 'AI Ops Multi-agent Orchestrator',
                    'target' => 'GENERAL',
                    'level' => 2,
                    'studi' => 'Asisten troubleshooting untuk NOC kampus, Respon insiden cloud dengan natural language',
                    'tags' => [
                        'teknologi' => ['LangGraph', 'OpenSearch', 'Kubernetes'],
                        'metode' => ['Multi-agent Reasoning', 'Playbook Automation', 'Incident Intelligence'],
                    ],
                ],
            ],
            'DATA' => [
                [
                    'kode' => 'DATA-01',
                    'nama' => 'Streaming Analytics untuk Decision Intelligence',
                    'deskripsi' => 'Membangun pipeline data real-time dari Kafka ke lakehouse untuk insight operasional instan.',
                    'tujuan' => 'Memberikan dashboard detik-ke-detik bagi manajemen operasi.',
                    'tipe' => 'Real-time Decision Intelligence Stack',
                    'target' => 'ANALIS',
                    'level' => 3,
                    'studi' => 'Monitoring supply cold-chain, Analitik fraud transaksi e-wallet',
                    'tags' => [
                        'teknologi' => ['Kafka', 'Apache Flink', 'Delta Lake', 'dbt'],
                        'metode' => ['Streaming ETL', 'Anomaly Detection', 'Data Contracts'],
                    ],
                ],
                [
                    'kode' => 'DATA-02',
                    'nama' => 'DataOps Orchestration Hub',
                    'deskripsi' => 'Menstandarkan pipeline batch dan streaming dengan template, CI/CD data, dan observability.',
                    'tujuan' => 'Mengurangi error data pipeline lintas tim.',
                    'tipe' => 'DataOps Automation Platform',
                    'target' => 'ARCHITECT',
                    'level' => 2,
                    'studi' => 'Pusat data perguruan tinggi terpadu, Integrasi data rumah sakit multi cabang',
                    'tags' => [
                        'teknologi' => ['Dagster', 'Prefect', 'Great Expectations', 'Snowflake'],
                        'metode' => ['DataOps', 'Orchestration', 'Data Observability'],
                    ],
                ],
                [
                    'kode' => 'DATA-03',
                    'nama' => 'Privacy-preserving Data Clean Room',
                    'deskripsi' => 'Kolaborasi analitik antar organisasi menggunakan differential privacy dan clean room.',
                    'tujuan' => 'Memungkinkan analisis lintas dataset tanpa membocorkan data mentah.',
                    'tipe' => 'Secure Data Collaboration Platform',
                    'target' => 'ANALIS',
                    'level' => 3,
                    'studi' => 'Analitik pemasaran antar brand ritel, Studi kesehatan multi rumah sakit',
                    'tags' => [
                        'teknologi' => ['Snowflake Clean Room', 'PyDP', 'AWS Clean Rooms'],
                        'metode' => ['Differential Privacy', 'Secure Multi-party Computation', 'Data Tokenization'],
                    ],
                ],
                [
                    'kode' => 'DATA-04',
                    'nama' => 'Knowledge Graph Insights Platform',
                    'deskripsi' => 'Menghubungkan data relasional dan tak terstruktur dalam graph untuk rekomendasi dan reasoning.',
                    'tujuan' => 'Memunculkan wawasan hubungan yang tidak terlihat di data tradisional.',
                    'tipe' => 'Knowledge Graph & Semantic Layer',
                    'target' => 'ANALIS',
                    'level' => 2,
                    'studi' => 'Graph rekomendasi riset kampus, Hubungan pemasok dalam rantai pasok',
                    'tags' => [
                        'teknologi' => ['Neo4j', 'RDF4J', 'Graph Data Science'],
                        'metode' => ['Graph Embedding', 'Link Prediction', 'Semantic Reasoning'],
                    ],
                ],
                [
                    'kode' => 'DATA-05',
                    'nama' => 'Synthetic Data Generator untuk Pengujian',
                    'deskripsi' => 'Membuat data tiruan berkualitas tinggi untuk menguji sistem tanpa mengekspos data asli.',
                    'tujuan' => 'Mempercepat pengujian sambil menjaga kepatuhan privasi.',
                    'tipe' => 'Synthetic Data Factory',
                    'target' => 'CREATOR',
                    'level' => 2,
                    'studi' => 'Dataset tiruan untuk pengujian core banking, Data latihan untuk AI layanan kesehatan',
                    'tags' => [
                        'teknologi' => ['SDV', 'CTGAN', 'Faker'],
                        'metode' => ['Privacy Evaluation', 'Statistical Matching', 'Data Masking'],
                    ],
                ],
            ],
            'CITRA' => [
                [
                    'kode' => 'CITRA-01',
                    'nama' => 'Edge Vision untuk Smart City',
                    'deskripsi' => 'Deploy model deteksi objek ringan di kamera jalan untuk mendukung kebijakan lalu lintas adaptif.',
                    'tujuan' => 'Mengurangi kemacetan dan pelanggaran jalan secara real-time.',
                    'tipe' => 'Edge Vision Analytics Stack',
                    'target' => 'ARCHITECT',
                    'level' => 3,
                    'studi' => 'Deteksi kendaraan ilegal di jalur busway, Penghitungan volume kendaraan di simpang padat',
                    'tags' => [
                        'teknologi' => ['NVIDIA Jetson', 'TensorRT', 'YOLOv8'],
                        'metode' => ['Model Pruning', 'Edge Deployment', 'Object Tracking'],
                    ],
                ],
                [
                    'kode' => 'CITRA-02',
                    'nama' => 'Vision Transformer untuk Triage Medis',
                    'deskripsi' => 'Memanfaatkan ViT & segmentasi untuk memprioritaskan kasus medis dari citra radiologi.',
                    'tujuan' => 'Membantu dokter menentukan kasus kritis lebih cepat.',
                    'tipe' => 'Medical Imaging Decision Support',
                    'target' => 'ANALIS',
                    'level' => 3,
                    'studi' => 'Seleksi kasus CT-stroke otomatis, Deteksi nodule paru menggunakan Swin Transformer',
                    'tags' => [
                        'teknologi' => ['Vision Transformer', 'MONAI', 'DICOM'],
                        'metode' => ['Semantic Segmentation', 'Grad-CAM', 'Federated Learning'],
                    ],
                ],
                [
                    'kode' => 'CITRA-03',
                    'nama' => '3D Reconstruction dengan Neural Radiance Field',
                    'deskripsi' => 'Menghasilkan replika 3D realistis dari foto 2D menggunakan NeRF untuk keperluan industri kreatif.',
                    'tujuan' => 'Mempercepat produksi aset 3D tanpa studio mahal.',
                    'tipe' => 'NeRF Content Lab',
                    'target' => 'CREATOR',
                    'level' => 3,
                    'studi' => 'Virtual tour cagar budaya, Visualisasi properti interaktif',
                    'tags' => [
                        'teknologi' => ['PyTorch3D', 'Instant-NGP', 'Blender'],
                        'metode' => ['Neural Radiance Field', 'Pose Estimation', 'Volumetric Rendering'],
                    ],
                ],
                [
                    'kode' => 'CITRA-04',
                    'nama' => 'Multimodal Citra-Teks Retrieval',
                    'deskripsi' => 'Menggabungkan embedding gambar dan teks (CLIP) untuk pencarian aset media yang akurat.',
                    'tujuan' => 'Memudahkan kurator konten menemukan materi sesuai narasi.',
                    'tipe' => 'Multimodal Asset Search Engine',
                    'target' => 'ANALIS',
                    'level' => 2,
                    'studi' => 'Pencarian arsip media penyiaran, Sistem rekomendasi moodboard desainer',
                    'tags' => [
                        'teknologi' => ['CLIP', 'Milvus', 'OpenSearch'],
                        'metode' => ['Contrastive Learning', 'Cross-modal Retrieval', 'Embedding Indexing'],
                    ],
                ],
                [
                    'kode' => 'CITRA-05',
                    'nama' => 'Drone Crop Health Computer Vision',
                    'deskripsi' => 'Pipeline analisis citra udara untuk mendeteksi stress tanaman dan rekomendasi tindakan.',
                    'tujuan' => 'Meningkatkan produktivitas pertanian presisi.',
                    'tipe' => 'Agri-vision Decision Platform',
                    'target' => 'ARCHITECT',
                    'level' => 2,
                    'studi' => 'Deteksi penyakit padi dari drone NDVI, Monitoring irigasi kebun sawit',
                    'tags' => [
                        'teknologi' => ['OpenCV', 'QGIS', 'DroneDeploy'],
                        'metode' => ['NDVI Analysis', 'Image Segmentation', 'Edge-post Processing'],
                    ],
                ],
            ],
            'NLP' => [
                [
                    'kode' => 'NLP-01',
                    'nama' => 'Retrieval-Augmented Chatbot Kampus',
                    'deskripsi' => 'Membangun chatbot akademik yang menggabungkan dokumen kampus dan LLM untuk menjawab pertanyaan mahasiswa.',
                    'tujuan' => 'Memberikan layanan informasi 24/7 tanpa overloading staf.',
                    'tipe' => 'Campus RAG Assistant',
                    'target' => 'CREATOR',
                    'level' => 2,
                    'studi' => 'Asisten kurikulum multi bahasa, FAQ beasiswa otomatis',
                    'tags' => [
                        'teknologi' => ['LangChain', 'HuggingFace Transformers', 'Pinecone'],
                        'metode' => ['RAG', 'Prompt Guardrails', 'Conversation Memory'],
                    ],
                ],
                [
                    'kode' => 'NLP-02',
                    'nama' => 'Multilingual Summarization LLM',
                    'deskripsi' => 'Mengadaptasi model ringkasan berita multi-bahasa Asia Tenggara dengan fine-tuning instruktional.',
                    'tujuan' => 'Menyediakan ringkasan cepat lintas bahasa untuk pembuat kebijakan.',
                    'tipe' => 'Multilingual Summarization Service',
                    'target' => 'ANALIS',
                    'level' => 3,
                    'studi' => 'Ringkasan laporan pemerintah, Digest berita maritim bilingual',
                    'tags' => [
                        'teknologi' => ['mT5', 'LoRA', 'SentencePiece'],
                        'metode' => ['Instruction Tuning', 'Evaluation with BLEURT', 'Knowledge Distillation'],
                    ],
                ],
                [
                    'kode' => 'NLP-03',
                    'nama' => 'Bias & Toxicity Audit Toolkit',
                    'deskripsi' => 'Framework untuk mengukur bias, toksisitas, dan fairness pada model bahasa lokal.',
                    'tujuan' => 'Memastikan penerapan AI bahasa yang etis.',
                    'tipe' => 'Responsible NLP Testing Suite',
                    'target' => 'ANALIS',
                    'level' => 2,
                    'studi' => 'Audit chatbot layanan publik, Evaluasi bias gender dalam summarizer',
                    'tags' => [
                        'teknologi' => ['Perspective API', 'HolisticBias', 'HateCheck'],
                        'metode' => ['Bias Benchmarking', 'Counterfactual Evaluation', 'Safety Scoring'],
                    ],
                ],
                [
                    'kode' => 'NLP-04',
                    'nama' => 'Speech-to-Action Assistive Agent',
                    'deskripsi' => 'Asisten suara untuk disabilitas yang menerjemahkan perintah menjadi aksi aplikasi desktop/web.',
                    'tujuan' => 'Meningkatkan aksesibilitas pengguna dengan keterbatasan motorik.',
                    'tipe' => 'Assistive Voice Agent',
                    'target' => 'CREATOR',
                    'level' => 2,
                    'studi' => 'Kontrol sistem akademik dengan bahasa Indonesia, Macro otomatis untuk pekerja remote',
                    'tags' => [
                        'teknologi' => ['Whisper', 'Rasa', 'Electron'],
                        'metode' => ['Intent Detection', 'Command Mapping', 'Few-shot Adaptation'],
                    ],
                ],
                [
                    'kode' => 'NLP-05',
                    'nama' => 'Low-resource Language Corpus Builder',
                    'deskripsi' => 'Membuat pipeline crawling, normalisasi, dan anotasi semi otomatis untuk bahasa daerah.',
                    'tujuan' => 'Memperluas ketersediaan dataset NLP bahasa lokal.',
                    'tipe' => 'Corpus Engineering Platform',
                    'target' => 'ARCHITECT',
                    'level' => 3,
                    'studi' => 'Korpus bahasa Aceh untuk summarization, Dataset chatbot Bali',
                    'tags' => [
                        'teknologi' => ['ElasticSearch', 'spaCy', 'Prodigy'],
                        'metode' => ['Active Learning', 'Data Valuation', 'Tokenizer Customization'],
                    ],
                ],
            ],
            'HCI' => [
                [
                    'kode' => 'HCI-01',
                    'nama' => 'Accessibility Computing Lab',
                    'deskripsi' => 'Mengevaluasi dan merancang ulang produk digital agar memenuhi WCAG 2.2 dengan partisipasi pengguna disabilitas.',
                    'tujuan' => 'Menghadirkan pengalaman setara untuk semua pengguna.',
                    'tipe' => 'Inclusive Design Toolkit',
                    'target' => 'ANALIS',
                    'level' => 2,
                    'studi' => 'Audit aplikasi pemerintahan, Redesign modul e-learning untuk low-vision',
                    'tags' => [
                        'teknologi' => ['Figma', 'Axe-core', 'Screen Reader Suite'],
                        'metode' => ['Inclusive Design', 'WCAG Testing', 'Participatory Design'],
                    ],
                ],
                [
                    'kode' => 'HCI-02',
                    'nama' => 'Neuroadaptive UI Experiment',
                    'deskripsi' => 'Menggunakan sensor biometrik & EEG ringan untuk menyesuaikan UI sesuai beban kognitif.',
                    'tujuan' => 'Mengurangi kelelahan digital dengan UI adaptif.',
                    'tipe' => 'Neuroadaptive UX Platform',
                    'target' => 'ARCHITECT',
                    'level' => 3,
                    'studi' => 'Dashboard trading adaptif, Sistem latihan VR anti dizziness',
                    'tags' => [
                        'teknologi' => ['Muse EEG', 'Unity', 'Python'],
                        'metode' => ['Affective Computing', 'Adaptive UI', 'Bio-signal Processing'],
                    ],
                ],
                [
                    'kode' => 'HCI-03',
                    'nama' => 'VR/AR Co-design Workspace',
                    'deskripsi' => 'Kolaborasi realtime antar desainer di ruang XR untuk membuat prototype produk.',
                    'tujuan' => 'Mempercepat proses design sprint jarak jauh.',
                    'tipe' => 'Immersive Collaboration Studio',
                    'target' => 'CREATOR',
                    'level' => 2,
                    'studi' => 'Workshop desain furnitur VR, Perancangan UI mobil otonom secara kolaboratif',
                    'tags' => [
                        'teknologi' => ['Unity', 'WebXR', 'Three.js'],
                        'metode' => ['Co-design', 'Immersive Prototyping', 'Spatial UX'],
                    ],
                ],
                [
                    'kode' => 'HCI-04',
                    'nama' => 'Conversational UX Analytics',
                    'deskripsi' => 'Menganalisis log chatbot/voice bot untuk meningkatkan empati dan efektivitas dialog.',
                    'tujuan' => 'Mengoptimalkan perjalanan percakapan pengguna.',
                    'tipe' => 'Conversation Intelligence Dashboard',
                    'target' => 'ANALIS',
                    'level' => 2,
                    'studi' => 'Evaluasi voice bot layanan publik, Analitik tonasi customer support AI',
                    'tags' => [
                        'teknologi' => ['Elastic Stack', 'PowerBI', 'Dialogflow'],
                        'metode' => ['Conversation Mining', 'Emotion Analysis', 'Journey Mapping'],
                    ],
                ],
                [
                    'kode' => 'HCI-05',
                    'nama' => 'Emotion-aware Learning Dashboard',
                    'deskripsi' => 'Menggabungkan data kamera & interaksi untuk menyesuaikan materi pembelajaran adaptif.',
                    'tujuan' => 'Mengurangi kebosanan belajar daring.',
                    'tipe' => 'Adaptive Learning Control Center',
                    'target' => 'CREATOR',
                    'level' => 2,
                    'studi' => 'Kelas hybrid dengan deteksi emosi, Tutor digital untuk anak berkebutuhan khusus',
                    'tags' => [
                        'teknologi' => ['TensorFlow', 'LiveKit', 'Supabase'],
                        'metode' => ['Affective UX', 'Adaptive Content', 'Learning Analytics'],
                    ],
                ],
            ],
            'GRAF' => [
                [
                    'kode' => 'GRAF-01',
                    'nama' => 'Digital Twin Visualization Platform',
                    'deskripsi' => 'Membuat visualisasi interaktif untuk digital twin pabrik atau gedung menggunakan data IoT langsung.',
                    'tujuan' => 'Mempercepat pengambilan keputusan melalui simulasi 3D real-time.',
                    'tipe' => 'Digital Twin Visualization Suite',
                    'target' => 'ARCHITECT',
                    'level' => 3,
                    'studi' => 'Twin smart campus, Monitoring energi PLTS industri',
                    'tags' => [
                        'teknologi' => ['Unity', 'Three.js', 'Cesium'],
                        'metode' => ['Realtime Rendering', 'Data-driven Animation', 'Scenario Simulation'],
                    ],
                ],
                [
                    'kode' => 'GRAF-02',
                    'nama' => 'XR Training Simulator untuk Industri',
                    'deskripsi' => 'Membuat pelatihan VR/AR dengan scenario branch untuk keselamatan kerja.',
                    'tujuan' => 'Mengurangi kecelakaan kerja melalui latihan imersif.',
                    'tipe' => 'XR Safety Training Platform',
                    'target' => 'CREATOR',
                    'level' => 3,
                    'studi' => 'Pelatihan tanggap darurat kilang, Simulasi prosedur operasi alat berat',
                    'tags' => [
                        'teknologi' => ['Unreal Engine', 'Quest 3', 'OpenXR'],
                        'metode' => ['Scenario-based Learning', 'Motion Capture', 'Haptics Integration'],
                    ],
                ],
                [
                    'kode' => 'GRAF-03',
                    'nama' => 'Procedural Content Generation Studio',
                    'deskripsi' => 'Toolkit untuk membuat dunia atau level game secara otomatis menggunakan rule dan AI.',
                    'tujuan' => 'Menghemat waktu produksi untuk game indie dan simulasi.',
                    'tipe' => 'Procedural Generation Toolkit',
                    'target' => 'CREATOR',
                    'level' => 2,
                    'studi' => 'Level game edukasi yang adaptif, Generasi kota virtual untuk film',
                    'tags' => [
                        'teknologi' => ['Godot', 'Blender Geometry Nodes', 'Python'],
                        'metode' => ['Procedural Generation', 'L-system', 'Genetic Algorithm'],
                    ],
                ],
                [
                    'kode' => 'GRAF-04',
                    'nama' => 'Immersive Storytelling Platform',
                    'deskripsi' => 'Menggabungkan suara spatial, interaksi gesture, serta narasi non-linear untuk museum digital.',
                    'tujuan' => 'Menghadirkan pengalaman cerita interaktif tingkat lanjut.',
                    'tipe' => 'Immersive Narrative Engine',
                    'target' => 'CREATOR',
                    'level' => 2,
                    'studi' => 'Pameran sejarah interaktif, Storytelling wisata metaverse',
                    'tags' => [
                        'teknologi' => ['WebXR', 'A-Frame', 'FMOD'],
                        'metode' => ['Non-linear Narrative', 'Spatial Audio', 'Experience Mapping'],
                    ],
                ],
                [
                    'kode' => 'GRAF-05',
                    'nama' => 'Volumetric Video Pipeline',
                    'deskripsi' => 'Pipeline produksi video volumetrik untuk konser atau olahraga agar penonton dapat berpindah sudut pandang.',
                    'tujuan' => 'Menciptakan pengalaman hiburan baru di XR.',
                    'tipe' => 'Volumetric Capture & Playback System',
                    'target' => 'ARCHITECT',
                    'level' => 3,
                    'studi' => 'Siara konser XR, Analitik gerakan atlet dari volumetric video',
                    'tags' => [
                        'teknologi' => ['Azure Kinect', 'DepthKit', 'Unreal'],
                        'metode' => ['Point Cloud Processing', 'Compression Pipeline', 'XR Streaming'],
                    ],
                ],
            ],
            'JAR' => [
                [
                    'kode' => 'JAR-01',
                    'nama' => 'Zero Trust Network Automation',
                    'deskripsi' => 'Merancang kebijakan akses mikro berbasis identitas perangkat dan perilaku pengguna.',
                    'tujuan' => 'Mengganti perimeter security klasik dengan Zero Trust modern.',
                    'tipe' => 'Zero Trust Policy Engine',
                    'target' => 'ARCHITECT',
                    'level' => 3,
                    'studi' => 'Implementasi kampus hybrid work, Segmentasi microservice per tenant',
                    'tags' => [
                        'teknologi' => ['Istio', 'Zitadel', 'Calico'],
                        'metode' => ['Zero Trust', 'Micro Segmentation', 'Policy Automation'],
                    ],
                ],
                [
                    'kode' => 'JAR-02',
                    'nama' => 'AI-driven Threat Hunting & SOC Co-pilot',
                    'deskripsi' => 'Memakai ML dan rule matriks MITRE ATT&CK untuk memprioritaskan alert dan saran respon.',
                    'tujuan' => 'Mengurangi noise SOC dan waktu respon insiden.',
                    'tipe' => 'Threat Hunting Intelligence Platform',
                    'target' => 'ANALIS',
                    'level' => 3,
                    'studi' => 'SOC kampus modern, Monitoring BUMN energi',
                    'tags' => [
                        'teknologi' => ['Elastic SIEM', 'MISP', 'Python'],
                        'metode' => ['ATT&CK Mapping', 'Behavior Analytics', 'Intelligent Triage'],
                    ],
                ],
                [
                    'kode' => 'JAR-03',
                    'nama' => 'Quantum-safe VPN Toolkit',
                    'deskripsi' => 'Menyiapkan VPN dan channel komunikasi yang siap menghadapi ancaman komputasi kuantum.',
                    'tujuan' => 'Melindungi data jangka panjang dari serangan harvest-now-decrypt-later.',
                    'tipe' => 'Post-Quantum Secure Communication',
                    'target' => 'ARCHITECT',
                    'level' => 2,
                    'studi' => 'VPN pemerintah dengan algoritma CRYSTALS-Kyber, Channel rahasia penelitian',
                    'tags' => [
                        'teknologi' => ['OpenVPN', 'CRYSTALS-Kyber', 'WireGuard'],
                        'metode' => ['Post-Quantum Cryptography', 'Hybrid Key Exchange', 'Forward Secrecy'],
                    ],
                ],
                [
                    'kode' => 'JAR-04',
                    'nama' => 'OT/ICS Security Digital Twin',
                    'deskripsi' => 'Model digital untuk simulasi serangan pada infrastruktur industri guna menguji kontrol keamanan.',
                    'tujuan' => 'Memberikan latihan aman bagi operator OT.',
                    'tipe' => 'ICS Cyber Range & Digital Twin',
                    'target' => 'ARCHITECT',
                    'level' => 3,
                    'studi' => 'Simulasi serangan PLC pabrik semen, Latihan pemadaman listrik',
                    'tags' => [
                        'teknologi' => ['S7comm', 'OPC UA', 'Docker'],
                        'metode' => ['Cyber Range', 'Attack Simulation', 'Anomaly Detection'],
                    ],
                ],
                [
                    'kode' => 'JAR-05',
                    'nama' => 'Secure SASE untuk Kampus Hybrid',
                    'deskripsi' => 'Menggabungkan SD-WAN, CASB, dan ZTNA untuk mendukung kuliah campuran on/off-campus.',
                    'tujuan' => 'Menyediakan akses aman dari mana saja.',
                    'tipe' => 'SASE Reference Implementation',
                    'target' => 'GENERAL',
                    'level' => 2,
                    'studi' => 'Akses aman laboratorium virtual, Remote exam proctoring',
                    'tags' => [
                        'teknologi' => ['Prisma Access', 'OpenZiti', 'WireGuard'],
                        'metode' => ['SASE', 'ZTNA', 'SD-WAN Optimization'],
                    ],
                ],
            ],
            'IOT' => [
                [
                    'kode' => 'IOT-01',
                    'nama' => 'Edge AI Smart Farming LoRa Mesh',
                    'deskripsi' => 'Jaringan sensor pertanian dengan LoRa mesh dan model AI lokal untuk rekomendasi penyiraman.',
                    'tujuan' => 'Menghemat air & pupuk di lahan luas.',
                    'tipe' => 'Smart Farming Edge Network',
                    'target' => 'ARCHITECT',
                    'level' => 3,
                    'studi' => 'Perkebunan tebu presisi, Monitoring kakao di daerah terpencil',
                    'tags' => [
                        'teknologi' => ['LoRaWAN', 'ESP32', 'Edge Impulse'],
                        'metode' => ['Edge AI', 'Soil Moisture Modeling', 'Federated Update'],
                    ],
                ],
                [
                    'kode' => 'IOT-02',
                    'nama' => 'Blockchain-backed Device Identity',
                    'deskripsi' => 'Sistem identitas perangkat IoT dan supply chain sensor berbasis blockchain untuk menghindari pemalsuan.',
                    'tujuan' => 'Menjamin integritas data sensor kritis.',
                    'tipe' => 'IoT Trust & Identity Platform',
                    'target' => 'ANALIS',
                    'level' => 3,
                    'studi' => 'Pelacakan vaksin rantai dingin, Sertifikasi sensor industri',
                    'tags' => [
                        'teknologi' => ['Hyperledger Fabric', 'DID', 'Azure DPS'],
                        'metode' => ['Decentralized Identity', 'Secure Provisioning', 'Attestation'],
                    ],
                ],
                [
                    'kode' => 'IOT-03',
                    'nama' => 'Wearable Digital Health Twin',
                    'deskripsi' => 'Menggabungkan data wearable dengan model twin kesehatan untuk memberikan insight personal.',
                    'tujuan' => 'Membantu klinik memantau pasien kronis dari rumah.',
                    'tipe' => 'Personal Digital Health Twin',
                    'target' => 'CREATOR',
                    'level' => 2,
                    'studi' => 'Pemantauan pasca operasi jantung, Program kebugaran kampus',
                    'tags' => [
                        'teknologi' => ['WearOS', 'FHIR', 'TimeSeries DB'],
                        'metode' => ['Digital Twin', 'Anomaly Scoring', 'Telehealth Workflow'],
                    ],
                ],
                [
                    'kode' => 'IOT-04',
                    'nama' => 'Industrial IoT Predictive Maintenance Hub',
                    'deskripsi' => 'Platform gateway industri dengan analitik vibrasi dan integrasi CMMS.',
                    'tujuan' => 'Mengurangi downtime mesin manufaktur.',
                    'tipe' => 'Predictive Maintenance Gateway',
                    'target' => 'ARCHITECT',
                    'level' => 3,
                    'studi' => 'Monitoring turbin pabrik kimia, Sistem peringatan dini mesin tekstil',
                    'tags' => [
                        'teknologi' => ['AWS IoT Greengrass', 'InfluxDB', 'Node-RED'],
                        'metode' => ['Condition Monitoring', 'AutoML Regression', 'Root Cause Analysis'],
                    ],
                ],
                [
                    'kode' => 'IOT-05',
                    'nama' => 'Smart Campus Energy Orchestrator',
                    'deskripsi' => 'Mengontrol lampu, AC, dan panel surya kampus melalui platform IoT dengan algoritma penghematan energi.',
                    'tujuan' => 'Menurunkan biaya listrik kampus hingga 20%.',
                    'tipe' => 'Energy Orchestration & Control System',
                    'target' => 'ANALIS',
                    'level' => 2,
                    'studi' => 'Optimasi pendingin auditorium, Penjadwalan charging kendaraan listrik kampus',
                    'tags' => [
                        'teknologi' => ['Home Assistant', 'MQTT', 'Azure IoT Hub'],
                        'metode' => ['Demand Response', 'Energy Forecasting', 'Rule-based Automation'],
                    ],
                ],
            ],
        ];
    }
}


