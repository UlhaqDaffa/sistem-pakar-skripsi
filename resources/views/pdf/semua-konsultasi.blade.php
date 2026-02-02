<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Semua Konsultasi - {{ $user->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.5;
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
            font-size: 22px;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .consultation-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .consultation-header {
            background-color: #3b82f6;
            color: white;
            padding: 10px 15px;
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 10px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 3px 10px 3px 0;
            width: 120px;
        }
        .info-value {
            display: table-cell;
            padding: 3px 0;
        }
        .result-box {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 10px;
            margin: 8px 0;
        }
        .result-box h3 {
            margin: 0 0 5px 0;
            color: #1e40af;
            font-size: 13px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 10px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
        }
        table th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
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
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
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
        <p>Laporan Semua Riwayat Konsultasi</p>
        <p>Pengguna: {{ $user->name }}</p>
        <p>Dicetak pada: {{ now()->format('d M Y, H:i') }}</p>
        <p>Total Konsultasi: {{ $consultations->count() }}</p>
    </div>

    @php
        $levelMap = [
            1 => 'Lv1 • Konseptual',
            2 => 'Lv2 • Pengembangan',
            3 => 'Lv3 • Deep Tech',
        ];
        $targetMap = [
            'CREATOR' => 'Creator',
            'ANALIS' => 'Analis',
            'ARCHITECT' => 'Architect',
            'GENERAL' => 'General',
        ];
    @endphp

    @foreach($consultations as $index => $consultation)
        <div class="consultation-section {{ $index > 0 ? 'page-break' : '' }}">
            <div class="consultation-header">
                Konsultasi #{{ $consultation->id }} - {{ $consultation->created_at->format('d M Y, H:i') }}
            </div>

            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Tanggal:</div>
                    <div class="info-value">{{ $consultation->created_at->format('d M Y, H:i') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status:</div>
                    <div class="info-value">{{ ucfirst($consultation->status) }}</div>
                </div>
            </div>

            @if($consultation->areaRisetFinal)
                <div class="result-box">
                    <h3>Rekomendasi Final: {{ $consultation->areaRisetFinal->nama_area }}</h3>
                    <p style="font-size: 10px;">{{ \Illuminate\Support\Str::limit($consultation->areaRisetFinal->deskripsi, 200) }}</p>
                    <p style="font-size: 10px; margin-top: 6px;">
                        Target: {{ $targetMap[$consultation->areaRisetFinal->target_arketipe] ?? '-' }} |
                        Level: {{ $levelMap[$consultation->areaRisetFinal->level_kesulitan] ?? '-' }}
                    </p>
                </div>
            @endif

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

            @if($consultation->nilaiMataKuliah->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th style="width: 70%;">Mata Kuliah</th>
                            <th style="width: 30%; text-align: center;">Nilai</th>
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
            @endif
        </div>
    @endforeach

    <div class="footer">
        <p>Dokumen ini dihasilkan secara otomatis oleh Sistem Pakar Rekomendasi Topik Penelitian</p>
        <p>Total {{ $consultations->count() }} konsultasi | Halaman {PAGENO} dari {nbpg}</p>
    </div>
</body>
</html>

