<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Konsultasi - {{ $consultation->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #1e40af;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background-color: #3b82f6;
            color: white;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 5px 10px 5px 0;
            width: 150px;
        }
        .info-value {
            display: table-cell;
            padding: 5px 0;
        }
        .result-box {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 10px 0;
        }
        .result-box h3 {
            margin: 0 0 10px 0;
            color: #1e40af;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-blue {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .badge-green {
            background-color: #d1fae5;
            color: #065f46;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sistem Pakar Rekomendasi Topik Penelitian</h1>
        <p>Laporan Hasil Konsultasi</p>
        <p>Dicetak pada: {{ now()->format('d M Y, H:i') }}</p>
    </div>

    <!-- Informasi Umum -->
    <div class="section">
        <div class="section-title">Informasi Umum</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nama Pengguna:</div>
                <div class="info-value">{{ $user->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $user->email }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal Konsultasi:</div>
                <div class="info-value">{{ $consultation->created_at->format('d M Y, H:i') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">{{ ucfirst($consultation->status) }}</div>
            </div>
        </div>
    </div>

    <!-- Hasil Rekomendasi -->
    @if($consultation->areaRisetFinal)
        <div class="section">
            <div class="section-title">Rekomendasi Final</div>
            <div class="result-box">
                <h3>{{ $consultation->areaRisetFinal->nama_area }}</h3>
                <p>{{ $consultation->areaRisetFinal->deskripsi }}</p>
            </div>
        </div>
    @endif

    <!-- Hasil Minat & Akademik -->
    <div class="section">
        <div class="section-title">Hasil Analisis</div>
        <div class="info-grid">
            @if($consultation->hasilMinat)
                <div class="info-row">
                    <div class="info-label">Hasil Minat:</div>
                    <div class="info-value">
                        <span class="badge badge-blue">{{ $consultation->hasilMinat->nama_area }}</span>
                    </div>
                </div>
            @endif
            @if($consultation->hasilAkademik)
                <div class="info-row">
                    <div class="info-label">Hasil Akademik:</div>
                    <div class="info-value">
                        <span class="badge badge-green">{{ $consultation->hasilAkademik->nama_area }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Nilai Mata Kuliah -->
    @if($consultation->nilaiMataKuliah->count() > 0)
        <div class="section">
            <div class="section-title">Nilai Mata Kuliah</div>
            <table>
                <thead>
                    <tr>
                        <th>Mata Kuliah</th>
                        <th style="text-align: center;">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($consultation->nilaiMataKuliah as $nilai)
                        <tr>
                            <td>{{ $nilai->mataKuliahKunci->nama_mata_kuliah }}</td>
                            <td style="text-align: center; font-weight: bold;">{{ number_format($nilai->nilai, 1) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Peta Inspirasi Penelitian -->
    @if($consultation->areaRisetFinal)
        <div class="section page-break">
            <div class="section-title">Peta Inspirasi Penelitian</div>
            
            <div style="margin-bottom: 20px;">
                <h3 style="color: #1e40af; font-size: 14px; margin-bottom: 10px;">1. Kesimpulan Utama</h3>
                <p><strong>{{ $consultation->areaRisetFinal->nama_area }}</strong></p>
                <p>{{ $consultation->areaRisetFinal->deskripsi }}</p>
            </div>

            <div style="margin-bottom: 20px;">
                <h3 style="color: #1e40af; font-size: 14px; margin-bottom: 10px;">2. Deskripsi</h3>
                <p>{{ $consultation->areaRisetFinal->deskripsi }}</p>
            </div>

            <div style="margin-bottom: 20px;">
                <h3 style="color: #1e40af; font-size: 14px; margin-bottom: 10px;">3. Teknologi & Metode Kunci</h3>
                <p><strong>Teknologi Utama:</strong></p>
                <p>{{ $consultation->areaRisetFinal->kata_kunci_teknologi }}</p>
                <p style="margin-top: 10px;"><strong>Metode Kunci:</strong></p>
                <p>{{ $consultation->areaRisetFinal->kata_kunci_metode }}</p>
            </div>

            <div style="margin-bottom: 20px;">
                <h3 style="color: #1e40af; font-size: 14px; margin-bottom: 10px;">4. Contoh Studi Kasus</h3>
                <p>{{ $consultation->areaRisetFinal->contoh_studi_kasus }}</p>
            </div>

            <div style="margin-bottom: 20px;">
                <h3 style="color: #1e40af; font-size: 14px; margin-bottom: 10px;">5. Formulasi Judul</h3>
                <p style="font-style: italic; margin: 5px 0;">"Penerapan Metode Hybrid Filtering untuk Sistem Rekomendasi Produk E-Commerce (Studi Kasus: {{ explode(',', $consultation->areaRisetFinal->contoh_studi_kasus)[0] ?? 'Studi Kasus' }})."</p>
                <p style="font-style: italic; margin: 5px 0;">"Analisis Perbandingan Akurasi Metode Collaborative Filtering dan Hybrid Filtering dalam Merekomendasikan Judul Skripsi."</p>
                <p style="font-style: italic; margin: 5px 0;">"Pengembangan Sistem Rekomendasi Pemilihan Karir Menggunakan Pendekatan Hybrid Berbasis Web."</p>
            </div>
        </div>
    @endif

    <!-- Jawaban Kuesioner -->
    @if($consultation->jawabanKonsultasis->count() > 0)
        <div class="section page-break">
            <div class="section-title">Jawaban Kuesioner</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%;">Kategori</th>
                        <th style="width: 50%;">Pertanyaan</th>
                        <th style="width: 35%;">Jawaban</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($consultation->jawabanKonsultasis as $jawaban)
                        <tr>
                            <td>{{ ucfirst($jawaban->opsiJawaban->pertanyaan->kategori->tipe ?? 'N/A') }}</td>
                            <td>{{ $jawaban->opsiJawaban->pertanyaan->teks_pertanyaan ?? 'N/A' }}</td>
                            <td>{{ $jawaban->opsiJawaban->teks_jawaban }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        <p>Dokumen ini dihasilkan secara otomatis oleh Sistem Pakar Rekomendasi Topik Penelitian</p>
        <p>Halaman {PAGENO} dari {nbpg}</p>
    </div>
</body>
</html>

