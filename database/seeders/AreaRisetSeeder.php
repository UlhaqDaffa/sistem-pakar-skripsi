<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AreaRiset;
use App\Models\MinatBidang;
use Illuminate\Support\Facades\DB;

class AreaRisetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('area_riset_minat_bidang')->truncate();
        AreaRiset::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get Minat Bidang
        $rpl = MinatBidang::where('kode_bidang', 'RPL')->firstOrFail();
        $peng = MinatBidang::where('kode_bidang', 'PENG')->firstOrFail();
        $ai = MinatBidang::where('kode_bidang', 'AI')->firstOrFail();
        $data = MinatBidang::where('kode_bidang', 'DATA')->firstOrFail();
        $citra = MinatBidang::where('kode_bidang', 'CITRA')->firstOrFail();
        $nlp = MinatBidang::where('kode_bidang', 'NLP')->firstOrFail();
        $hci = MinatBidang::where('kode_bidang', 'HCI')->firstOrFail();
        $graf = MinatBidang::where('kode_bidang', 'GRAF')->firstOrFail();
        $jar = MinatBidang::where('kode_bidang', 'JAR')->firstOrFail();
        $iot = MinatBidang::where('kode_bidang', 'IOT')->firstOrFail();

        // === RPL: Rekayasa Perangkat Lunak ===
        $rpl01 = AreaRiset::create([
            'kode_area' => 'RPL-01',
            'nama_area' => 'Arsitektur Microservices',
            'deskripsi' => 'Penelitian tentang pemecahan aplikasi monolitik menjadi layanan-layanan kecil yang independen untuk meningkatkan skalabilitas, fleksibilitas, dan maintainability.',
            'kata_kunci_teknologi' => 'Docker, Kubernetes, API Gateway, RabbitMQ, Kafka, gRPC, REST API',
            'kata_kunci_metode' => 'Domain-Driven Design (DDD), SAGA Pattern, CQRS, Service Discovery',
            'contoh_studi_kasus' => 'Migrasi sistem e-commerce monolitik ke arsitektur microservices untuk menangani flash sale',
        ]);
        $rpl01->minatBidangs()->attach($rpl->id);

        $rpl02 = AreaRiset::create([
            'kode_area' => 'RPL-02',
            'nama_area' => 'Metodologi Pengembangan & DevOps',
            'deskripsi' => 'Studi tentang efektivitas metodologi (spt Agile, Scrum) atau implementasi budaya DevOps untuk mempercepat siklus rilis dan meningkatkan kolaborasi tim.',
            'kata_kunci_teknologi' => 'Git, Jenkins, GitLab CI/CD, Jira, Docker, Ansible',
            'kata_kunci_metode' => 'Agile, Scrum, Kanban, DevOps, CI/CD (Continuous Integration/Continuous Delivery)',
            'contoh_studi_kasus' => 'Analisis dampak penerapan CI/CD terhadap pengurangan bug dan percepatan deployment di startup X',
        ]);
        $rpl02->minatBidangs()->attach($rpl->id);

        $rpl03 = AreaRiset::create([
            'kode_area' => 'RPL-03',
            'nama_area' => 'Pengujian & Kualitas Perangkat Lunak',
            'deskripsi' => 'Fokus pada teknik dan otomatisasi pengujian (testing) untuk menjamin kualitas, menemukan bug, dan memastikan software bebas dari error kritis.',
            'kata_kunci_teknologi' => 'Selenium, Appium, JMeter, Postman, JUnit, Cypress, Katalon',
            'kata_kunci_metode' => 'Automation Testing, Black-Box Testing, White-Box Testing, TDD (Test-Driven Development), Load Testing',
            'contoh_studi_kasus' => 'Pengembangan script pengujian otomatis untuk fitur registrasi dan transaksi pada aplikasi mobile banking',
        ]);
        $rpl03->minatBidangs()->attach($rpl->id);

        // === PENG: Pengembangan Aplikasi ===
        $peng01 = AreaRiset::create([
            'kode_area' => 'PENG-01',
            'nama_area' => 'Pengembangan Aplikasi Mobile Cross-platform',
            'deskripsi' => 'Membangun aplikasi untuk platform Android dan iOS menggunakan satu codebase (spt Flutter atau React Native) untuk efisiensi pengembangan.',
            'kata_kunci_teknologi' => 'Flutter, React Native, Dart, JavaScript, Firebase, SQLite',
            'kata_kunci_metode' => 'Declarative UI, State Management (Provider, Redux, Bloc), Integrasi API',
            'contoh_studi_kasus' => 'Pembuatan aplikasi e-learning atau sistem absensi online cross-platform menggunakan Flutter',
        ]);
        $peng01->minatBidangs()->attach($peng->id);

        $peng02 = AreaRiset::create([
            'kode_area' => 'PENG-02',
            'nama_area' => 'Pengembangan Aplikasi Web (Full-stack)',
            'deskripsi' => 'Merancang dan mengimplementasikan sisi frontend (UI) dan backend (logika server, database) dari sebuah aplikasi web secara terintegrasi.',
            'kata_kunci_teknologi' => 'Laravel, Node.js (Express), React, Vue.js, MySQL, PostgreSQL, TailwindCSS',
            'kata_kunci_metode' => 'REST API, MVC (Model-View-Controller), Server-Side Rendering (SSR), Single Page Application (SPA)',
            'contoh_studi_kasus' => 'Sistem informasi geografis (GIS) pemetaan UMKM berbasis web menggunakan MERN stack (MongoDB, Express, React, Node)',
        ]);
        $peng02->minatBidangs()->attach($peng->id);

        $peng03 = AreaRiset::create([
            'kode_area' => 'PENG-03',
            'nama_area' => 'Progressive Web Apps (PWA)',
            'deskripsi' => 'Penelitian tentang aplikasi web yang memberikan pengalaman seperti aplikasi native, termasuk kemampuan offline, push notification, dan instalasi di homescreen.',
            'kata_kunci_teknologi' => 'Service Workers, Web App Manifest, Cache API, JavaScript, Workbox',
            'kata_kunci_metode' => 'Offline-first, Push Notification API, Background Sync',
            'contoh_studi_kasus' => 'Implementasi PWA pada website toko online untuk meningkatkan konversi dan user engagement',
        ]);
        $peng03->minatBidangs()->attach($peng->id);

        // === AI: Kecerdasan Buatan ===
        $ai01 = AreaRiset::create([
            'kode_area' => 'AI-01',
            'nama_area' => 'Machine Learning (Klasifikasi/Prediksi)',
            'deskripsi' => 'Menggunakan algoritma untuk melatih model agar dapat mengklasifikasikan data ke dalam kategori tertentu atau memprediksi hasil di masa depan berdasarkan data historis.',
            'kata_kunci_teknologi' => 'Python, Scikit-learn, TensorFlow, Pandas, Jupyter',
            'kata_kunci_metode' => 'Decision Tree (CART, C4.5), K-Nearest Neighbors (KNN), Support Vector Machine (SVM), Regresi Linier/Logistik, Naive Bayes',
            'contoh_studi_kasus' => 'Prediksi kelulusan mahasiswa tepat waktu menggunakan algoritma C4.5 berdasarkan data akademik',
        ]);
        $ai01->minatBidangs()->attach($ai->id);

        $ai02 = AreaRiset::create([
            'kode_area' => 'AI-02',
            'nama_area' => 'Deep Learning (Neural Networks)',
            'deskripsi' => 'Penerapan jaringan syaraf tiruan dengan banyak lapisan (deep neural networks) untuk menyelesaikan masalah kompleks seperti pengenalan gambar atau bahasa.',
            'kata_kunci_teknologi' => 'TensorFlow, Keras, PyTorch, OpenCV',
            'kata_kunci_metode' => 'Artificial Neural Network (ANN), Convolutional Neural Network (CNN), Recurrent Neural Network (RNN)',
            'contoh_studi_kasus' => 'Klasifikasi jenis kendaraan di jalan tol secara real-time menggunakan CNN',
        ]);
        $ai02->minatBidangs()->attach($ai->id);

        $ai03 = AreaRiset::create([
            'kode_area' => 'AI-03',
            'nama_area' => 'Sistem Pakar & Logika Fuzzy',
            'deskripsi' => 'Membangun sistem berbasis pengetahuan (knowledge-based) untuk meniru penalaran seorang pakar atau menangani ketidakpastian data menggunakan logika fuzzy.',
            'kata_kunci_teknologi' => 'PHP, Python, (Framework internal), MATLAB',
            'kata_kunci_metode' => 'Rule-Based, Forward Chaining, Backward Chaining, Certainty Factor, Fuzzy (Mamdan, Sugeno)',
            'contoh_studi_kasus' => 'Sistem pakar untuk diagnosis dini penyakit tanaman padi menggunakan metode Certainty Factor',
        ]);
        $ai03->minatBidangs()->attach($ai->id);

        $ai04 = AreaRiset::create([
            'kode_area' => 'AI-04',
            'nama_area' => 'Algoritma Optimasi (Evolusioner)',
            'deskripsi' => 'Menggunakan algoritma yang terinspirasi dari alam (seperti evolusi atau perilaku koloni) untuk menemukan solusi terbaik dari sebuah masalah penjadwalan atau pencarian rute.',
            'kata_kunci_teknologi' => 'Python, MATLAB',
            'kata_kunci_metode' => 'Algoritma Genetika, Particle Swarm Optimization (PSO), Ant Colony Optimization (ACO)',
            'contoh_studi_kasus' => 'Optimasi penjadwalan mata kuliah di universitas menggunakan Algoritma Genetika',
        ]);
        $ai04->minatBidangs()->attach($ai->id);

        // === DATA: Sains Data & Big Data ===
        $data01 = AreaRiset::create([
            'kode_area' => 'DATA-01',
            'nama_area' => 'Data Mining & Text Mining',
            'deskripsi' => 'Fokus pada penerapan algoritma untuk menemukan pola tersembunyi (hidden patterns) dan asosiasi (aturan) dari dalam dataset besar, baik terstruktur (database) maupun tidak terstruktur (teks).',
            'kata_kunci_teknologi' => 'Python (Pandas, Scikit-learn), R, Weka, RapidMiner, NLTK, Sastrawi',
            'kata_kunci_metode' => 'Clustering (K-Means, DBSCAN), Asosiasi (Market Basket Analysis, Apriori), Klasifikasi, Preprocessing Data, TF-IDF',
            'contoh_studi_kasus' => 'Analisis pola belanja pelanggan di supermarket untuk menentukan tata letak produk menggunakan algoritma Apriori',
        ]);
        $data01->minatBidangs()->attach($data->id);

        $data02 = AreaRiset::create([
            'kode_area' => 'DATA-02',
            'nama_area' => 'Sistem Rekomendasi (Recommender Systems)',
            'deskripsi' => 'Merancang sistem yang dapat memberikan saran atau rekomendasi item (produk, film, berita) secara personal kepada pengguna berdasarkan preferensi atau perilaku masa lalu.',
            'kata_kunci_teknologi' => 'Python (Surprise, Scikit-learn), FastAPI, MySQL, MongoDB',
            'kata_kunci_metode' => 'Collaborative Filtering (User-based, Item-based), Content-Based Filtering, Hybrid Filtering',
            'contoh_studi_kasus' => 'Pembuatan sistem rekomendasi topik penelitian untuk mahasiswa (seperti sistem Anda) menggunakan hybrid filtering',
        ]);
        $data02->minatBidangs()->attach($data->id);

        $data03 = AreaRiset::create([
            'kode_area' => 'DATA-03',
            'nama_area' => 'Analisis Sentimen',
            'deskripsi' => 'Menggunakan text mining dan NLP untuk mengklasifikasikan opini atau emosi (positif, negatif, netral) dari data teks, seperti ulasan produk atau tweet media sosial.',
            'kata_kunci_teknologi' => 'Python (NLTK, Scikit-learn, Sastrawi), Twitter API',
            'kata_kunci_metode' => 'Lexicon-Based, Machine Learning (Naive Bayes, SVM), Deep Learning (RNN, LSTM), Word Embedding',
            'contoh_studi_kasus' => 'Analisis sentimen publik terhadap kebijakan pemerintah baru berdasarkan data dari platform Twitter',
        ]);
        $data03->minatBidangs()->attach($data->id);

        // === CITRA: Pemrosesan Citra & Visi Komputer ===
        $citra01 = AreaRiset::create([
            'kode_area' => 'CITRA-01',
            'nama_area' => 'Pengenalan Objek (Object Detection/Recognition)',
            'deskripsi' => 'Melatih model untuk dapat mengidentifikasi dan melokalisasi (memberi kotak) objek-objek tertentu di dalam sebuah gambar atau video secara real-time.',
            'kata_kunci_teknologi' => 'Python, OpenCV, TensorFlow, Keras, YOLO (You Only Look Once)',
            'kata_kunci_metode' => 'CNN (Convolutional Neural Network), R-CNN, SSD (Single Shot MultiBox Detector), YOLO',
            'contoh_studi_kasus' => 'Sistem penghitung jumlah kendaraan di persimpangan jalan raya menggunakan YOLO dan kamera CCTV',
        ]);
        $citra01->minatBidangs()->attach($citra->id);

        $citra02 = AreaRiset::create([
            'kode_area' => 'CITRA-02',
            'nama_area' => 'Analisis Citra Medis',
            'deskripsi' => 'Penerapan deep learning dan pemrosesan citra untuk membantu diagnosis penyakit dengan menganalisis gambar medis seperti X-Ray, CT Scan, atau MRI.',
            'kata_kunci_teknologi' => 'Python, OpenCV, Keras, TensorFlow, Scikit-image, DICOM',
            'kata_kunci_metode' => 'Segmentasi Citra (U-Net), Klasifikasi (CNN), Ekstraksi Fitur, Image Enhancement',
            'contoh_studi_kasus' => 'Klasifikasi citra X-Ray paru-paru untuk deteksi dini pneumonia atau COVID-19 menggunakan CNN',
        ]);
        $citra02->minatBidangs()->attach($citra->id);

        $citra03 = AreaRiset::create([
            'kode_area' => 'CITRA-03',
            'nama_area' => 'Pengenalan Karakter & Wajah (OCR & Face Recognition)',
            'deskripsi' => 'Mengembangkan sistem yang dapat "membaca" teks dari gambar (OCR) atau mengidentifikasi individu berdasarkan fitur wajah mereka (face recognition).',
            'kata_kunci_teknologi' => 'Python, OpenCV, Tesseract, Dlib, face_recognition library',
            'kata_kunci_metode' => 'Ekstraksi Fitur (Haar Cascades, HOG), Deep Learning (Siamese Networks, FaceNet), Template Matching',
            'contoh_studi_kasus' => 'Sistem absensi mahasiswa otomatis menggunakan face recognition di ruang kelas',
        ]);
        $citra03->minatBidangs()->attach($citra->id);

        // === NLP: Pemrosesan Bahasa Alami ===
        $nlp01 = AreaRiset::create([
            'kode_area' => 'NLP-01',
            'nama_area' => 'Chatbot & Asisten Virtual',
            'deskripsi' => 'Membangun agen percakapan (conversational agent) yang dapat memahami pertanyaan pengguna dalam bahasa alami dan memberikan jawaban yang relevan.',
            'kata_kunci_teknologi' => 'Python, RASA, Google Dialogflow, Telegram Bot API, WhatsApp API',
            'kata_kunci_metode' => 'Intent Classification, Entity Extraction, Sequence-to-Sequence (Seq2Seq), Retrieval-based, Generative-based',
            'contoh_studi_kasus' => 'Pembuatan chatbot layanan pelanggan untuk e-commerce yang dapat menjawab pertanyaan seputar status pesanan',
        ]);
        $nlp01->minatBidangs()->attach($nlp->id);

        $nlp02 = AreaRiset::create([
            'kode_area' => 'NLP-02',
            'nama_area' => 'Penerjemahan & Ringkasan Teks',
            'deskripsi' => 'Menggunakan model deep learning (biasanya berbasis Transformer) untuk menerjemahkan teks dari satu bahasa ke bahasa lain, atau meringkas dokumen panjang menjadi poin-poin penting.',
            'kata_kunci_teknologi' => 'Python, TensorFlow, PyTorch, Hugging Face (Transformers, BERT, GPT)',
            'kata_kunci_metode' => 'Machine Translation (SMT, NMT), Text Summarization (Ekstraktif, Abstraktif), Attention Mechanism',
            'contoh_studi_kasus' => 'Sistem peringkas berita otomatis dari berbagai portal online menggunakan metode ekstraktif',
        ]);
        $nlp02->minatBidangs()->attach($nlp->id);

        $nlp03 = AreaRiset::create([
            'kode_area' => 'NLP-03',
            'nama_area' => 'Klasifikasi Teks & Deteksi Hoaks',
            'deskripsi' => 'Memanfaatkan machine learning untuk mengkategorikan dokumen teks secara otomatis, seperti memfilter email spam atau mengidentifikasi berita palsu (hoaks).',
            'kata_kunci_teknologi' => 'Python, Scikit-learn, NLTK, Sastrawi, Hugging Face',
            'kata_kunci_metode' => 'TF-IDF, Word Embedding (Word2Vec), Klasifikasi (Naive Bayes, SVM, BERT)',
            'contoh_studi_kasus' => 'Pengembangan plugin browser untuk mendeteksi judul berita hoaks menggunakan model klasifikasi teks',
        ]);
        $nlp03->minatBidangs()->attach($nlp->id);

        // === HCI: Desain UI/UX & Interaksi Manusia-Komputer ===
        $hci01 = AreaRiset::create([
            'kode_area' => 'HCI-01',
            'nama_area' => 'Evaluasi Usability (Pengujian Kegunaan)',
            'deskripsi' => 'Penelitian untuk mengukur tingkat kemudahan, efisiensi, dan kepuasan pengguna saat berinteraksi dengan sebuah interface aplikasi.',
            'kata_kunci_teknologi' => 'Figma, Sketch, Maze, Hotjar, Google Analytics, Perangkat Eye-tracking',
            'kata_kunci_metode' => 'Usability Testing, Heuristic Evaluation (Nielsen), Cognitive Walkthrough, SUS (System Usability Scale), Think Aloud Protocol',
            'contoh_studi_kasus' => 'Analisis perbandingan usability antara aplikasi mobile banking Bank A dan Bank B menggunakan metode SUS',
        ]);
        $hci01->minatBidangs()->attach($hci->id);

        $hci02 = AreaRiset::create([
            'kode_area' => 'HCI-02',
            'nama_area' => 'Desain Pengalaman Pengguna (User Experience - UX)',
            'deskripsi' => 'Fokus pada perancangan alur kerja, arsitektur informasi, dan prototype untuk memastikan keseluruhan pengalaman pengguna terasa logis, intuitif, dan memecahkan masalah.',
            'kata_kunci_teknologi' => 'Figma, Adobe XD, Balsamiq, Miro (untuk User Flow)',
            'kata_kunci_metode' => 'Design Thinking, User Persona, User Journey Mapping, Card Sorting, Wireframing, Prototyping',
            'contoh_studi_kasus' => 'Perancangan prototype high-fidelity aplikasi konsultasi kesehatan mental berbasis user persona dan journey mapping',
        ]);
        $hci02->minatBidangs()->attach($hci->id);

        $hci03 = AreaRiset::create([
            'kode_area' => 'HCI-03',
            'nama_area' => 'Gamification (Gamifikasi)',
            'deskripsi' => 'Menerapkan elemen dan mekanika desain game (seperti poin, badge, leaderboard) ke dalam konteks non-game untuk meningkatkan motivasi dan engagement pengguna.',
            'kata_kunci_teknologi' => '(Tergantung platform, misal: Laravel/PHP, JavaScript)',
            'kata_kunci_metode' => 'Points, Badges, Leaderboards (PBL), Octalysis Framework, Self-Determination Theory',
            'contoh_studi_kasus' => 'Implementasi gamifikasi pada platform e-learning untuk meningkatkan motivasi belajar mahasiswa',
        ]);
        $hci03->minatBidangs()->attach($hci->id);

        // === GRAF: Grafika Komputer & Multimedia ===
        $graf01 = AreaRiset::create([
            'kode_area' => 'GRAF-01',
            'nama_area' => 'Pengembangan Game (2D/3D)',
            'deskripsi' => 'Merancang dan membangun game interaktif, mencakup gameplay, grafis, audio, dan fisika di dalamnya.',
            'kata_kunci_teknologi' => 'Unity, Unreal Engine, Godot, Blender, C#, C++, Pygame',
            'kata_kunci_metode' => 'Game Design Document (GDD), Finite State Machine (FSM) (untuk AI musuh), Collision Detection, Shaders',
            'contoh_studi_kasus' => 'Pembuatan game edukasi (serious game) 3D untuk simulasi mitigasi bencana alam menggunakan Unity',
        ]);
        $graf01->minatBidangs()->attach($graf->id);

        $graf02 = AreaRiset::create([
            'kode_area' => 'GRAF-02',
            'nama_area' => 'Augmented Reality (AR)',
            'deskripsi' => 'Mengembangkan aplikasi yang menggabungkan objek virtual 2D/3D ke dalam lingkungan dunia nyata secara real-time melalui kamera smartphone atau perangkat khusus.',
            'kata_kunci_teknologi' => 'Unity, Vuforia, ARCore (Android), ARKit (iOS), Blender',
            'kata_kunci_metode' => 'Marker-based Tracking, Markerless Tracking (SLAM), Image Target',
            'contoh_studi_kasus' => 'Aplikasi AR untuk visualisasi furnitur di dalam ruangan rumah melalui kamera smartphone',
        ]);
        $graf02->minatBidangs()->attach($graf->id);

        $graf03 = AreaRiset::create([
            'kode_area' => 'GRAF-03',
            'nama_area' => 'Virtual Reality (VR)',
            'deskripsi' => 'Menciptakan lingkungan simulasi 3D yang imersif di mana pengguna dapat berinteraksi menggunakan perangkat headset VR.',
            'kata_kunci_teknologi' => 'Unity, Unreal Engine, Oculus SDK, SteamVR, Blender, 3ds Max',
            'kata_kunci_metode' => '3D Modelling, Environment Design, VR Interaction Design, Simulasi Fisika',
            'contoh_studi_kasus' => 'Pengembangan simulasi terapi VR untuk mengatasi fobia ketinggian (acrophobia)',
        ]);
        $graf03->minatBidangs()->attach($graf->id);

        // === JAR: Jaringan & Keamanan Siber ===
        $jar01 = AreaRiset::create([
            'kode_area' => 'JAR-01',
            'nama_area' => 'Keamanan Jaringan & Forensik Digital',
            'deskripsi' => 'Penelitian tentang teknik untuk melindungi infrastruktur jaringan dari serangan, atau menganalisis bukti digital (forensik) setelah terjadi insiden keamanan.',
            'kata_kunci_teknologi' => 'Wireshark, Nmap, Metasploit, Snort, pfSense, FTK Imager, Autopsy',
            'kata_kunci_metode' => 'Penetration Testing, Analisis Malware, Network Traffic Analysis, Intrusion Detection System (IDS), Firewall Configuration',
            'contoh_studi_kasus' => 'Analisis forensik digital pada file gambar untuk mengungkap pesan tersembunyi (steganografi)',
        ]);
        $jar01->minatBidangs()->attach($jar->id);

        $jar02 = AreaRiset::create([
            'kode_area' => 'JAR-02',
            'nama_area' => 'Kriptografi & Steganografi',
            'deskripsi' => 'Fokus pada ilmu penyandian pesan (Kriptografi) agar tidak dapat dibaca pihak lain, atau penyembunyian pesan di dalam media digital lain (Steganografi).',
            'kata_kunci_teknologi' => 'Python, MATLAB, OpenSSL',
            'kata_kunci_metode' => 'Algoritma (AES, RSA, DES), Hashing (SHA-256), Public Key Infrastructure (PKI), Least Significant Bit (LSB)',
            'contoh_studi_kasus' => 'Implementasi algoritma AES-256 untuk pengamanan data rekam medis pasien di aplikasi web',
        ]);
        $jar02->minatBidangs()->attach($jar->id);

        $jar03 = AreaRiset::create([
            'kode_area' => 'JAR-03',
            'nama_area' => 'Manajemen Jaringan & QoS',
            'deskripsi' => 'Studi tentang optimalisasi kinerja jaringan komputer, terutama dalam hal alokasi bandwidth dan prioritas data untuk layanan kritis (spt video call).',
            'kata_kunci_teknologi' => 'Cisco Packet Tracer, GNS3, Mikrotik RouterOS, PRTG Network Monitor',
            'kata_kunci_metode' => 'Quality of Service (QoS), Load Balancing, Traffic Shaping, Routing Protocols (OSPF, BGP), VPN',
            'contoh_studi_kasus' => 'Analisis kinerja routing protocol OSPF dan EIGRP pada jaringan enterprise menggunakan simulasi GNS3',
        ]);
        $jar03->minatBidangs()->attach($jar->id);

        // === IOT: Sistem Tertanam & Internet of Things ===
        $iot01 = AreaRiset::create([
            'kode_area' => 'IOT-01',
            'nama_area' => 'Sistem Smart Home / Smart Building',
            'deskripsi' => 'Mengembangkan sistem terintegrasi untuk mengontrol dan mengotomatisasi perangkat di rumah atau gedung (lampu, AC, keamanan) melalui internet.',
            'kata_kunci_teknologi' => 'Arduino, Raspberry Pi, ESP32/ESP8266, NodeMCU, Firebase, Blynk, MQTT',
            'kata_kunci_metode' => 'Wireless Sensor Network (WSN), Protokol (MQTT, HTTP), Real-time Data Processing',
            'contoh_studi_kasus' => 'Rancang bangun sistem kontrol lampu dan kunci pintu otomatis berbasis aplikasi mobile dan platform IoT Blynk',
        ]);
        $iot01->minatBidangs()->attach($iot->id);

        $iot02 = AreaRiset::create([
            'kode_area' => 'IOT-02',
            'nama_area' => 'Sistem Monitoring (Pertanian/Kesehatan/Lingkungan)',
            'deskripsi' => 'Fokus pada pengumpulan data dari sensor di lapangan secara real-time untuk pemantauan jarak jauh, seperti memantau kelembaban tanah, detak jantung pasien, atau kualitas udara.',
            'kata_kunci_teknologi' => 'Arduino, ESP32, LoRaWAN, Sensor (DHT22, pH, EKG), ThingsSpeak, Antares',
            'kata_kunci_metode' => 'Data Logging, Remote Sensing, Data Visualization Dashboard',
            'contoh_studi_kasus' => 'Sistem monitoring kualitas udara (PM2.5) berbasis IoT dan visualisasi dashboard web',
        ]);
        $iot02->minatBidangs()->attach($iot->id);

        $iot03 = AreaRiset::create([
            'kode_area' => 'IOT-03',
            'nama_area' => 'Robotika & Sistem Kontrol',
            'deskripsi' => 'Menggabungkan perangkat keras (mekanika), elektronika (sensor/aktuator), dan software (logika kontrol) untuk membuat robot atau sistem otomasi yang dapat bergerak atau melakukan tugas fisik.',
            'kata_kunci_teknologi' => 'Arduino, Raspberry Pi, Motor Servo, Sensor Ultrasonik, OpenCV (untuk navigasi)',
            'kata_kunci_metode' => 'Kontrol PID (Proportional-Integral-Derivative), Pathfinding (A*), Kinematics',
            'contoh_studi_kasus' => 'Pembuatan robot pemilah sampah otomatis berdasarkan jenis material menggunakan sensor dan machine learning',
        ]);
        $iot03->minatBidangs()->attach($iot->id);
    }
}
