<?php

namespace App\Services;

use App\Models\Konsultasi;
use App\Models\KonfigurasiPembobotan;
use Illuminate\Support\Collection;

class ScoringService
{
    /**
     * Mapping dari kode minat (dari kode_jawaban) ke kode_bidang
     * Mapping untuk 10 minat bidang yang baru
     */
    private array $minatToBidangMapping = [
        'RPL' => 'RPL',
        'PENG' => 'PENG',
        'GRAF' => 'GRAF',
        'AI' => 'AI',
        'DATA' => 'DATA',
        'CITRA' => 'CITRA',
        'NLP' => 'NLP',
        'HCI' => 'HCI',
        'JAR' => 'JAR',
        'IOT' => 'IOT',
    ];

    /**
     * Hitung total skor minat ter-bobot berdasarkan jawaban konsultasi.
     *
     * Mengembalikan satu angka (float) yang merepresentasikan kekuatan minat
     * gabungan user di seluruh bidang.
     */
    public function calculateHasilMinat(Konsultasi $konsultasi): float
    {
        // 1. Ambil semua jawaban konsultasi dengan relasi
        $jawabanKonsultasi = $konsultasi->jawabanKonsultasis()
            ->with(['opsiJawaban.pertanyaan.kategori'])
            ->get();

        // 2. Pisahkan jawaban minat dan asesmen
        $jawabanMinat = $this->getJawabanByKategori($jawabanKonsultasi, 'minat');
        $jawabanAsesmen = $this->getJawabanByKategori($jawabanKonsultasi, 'asesmen');

        // 3. Hitung skor per minat_bidang dari jawaban minat
        $skorMinatPerBidang = $this->calculateSkorMinat($jawabanMinat);

        // 4. Hitung skor asesmen per minat_bidang
        $skorAsesmenPerBidang = $this->calculateSkorAsesmen($jawabanAsesmen);

        // 6. Gabungkan skor dengan pembobotan per bidang
        $skorFinalPerBidang = [];
        $allBidangCodes = array_unique(array_merge(
            array_keys($skorMinatPerBidang),
            array_keys($skorAsesmenPerBidang)
        ));

        foreach ($allBidangCodes as $kodeBidang) {
            $skorMinat = $skorMinatPerBidang[$kodeBidang] ?? 0;
            $skorAsesmen = $skorAsesmenPerBidang[$kodeBidang] ?? 0;
            $skorFinalPerBidang[$kodeBidang] = ($skorMinat * $bobotMinat) + ($skorAsesmen * $bobotAsesmen);
        }

        // 7. Agregasi menjadi satu skor total (jumlah semua bidang)
        if (empty($skorFinalPerBidang)) {
            return 0.0;
        }

        $totalSkor = array_sum($skorFinalPerBidang);

        return (float) $totalSkor;
    }

    /**
     * Filter jawaban berdasarkan kategori
     */
    private function getJawabanByKategori(Collection $jawabanKonsultasi, string $kategoriTipe): Collection
    {
        return $jawabanKonsultasi->filter(function ($jawaban) use ($kategoriTipe) {
            $kategori = $jawaban->opsiJawaban->pertanyaan->kategori ?? null;
            return $kategori && $kategori->tipe === $kategoriTipe;
        });
    }

    /**
     * Hitung skor minat per minat_bidang
     */
    private function calculateSkorMinat(Collection $jawabanMinat): array
    {
        $skorPerBidang = [];

        foreach ($jawabanMinat as $jawaban) {
            $kodeJawaban = $jawaban->opsiJawaban->kode_jawaban;
            
            // Extract minat code dari kode_jawaban (e.g., "WEB" dari "MINAT_WEB")
            if (!str_starts_with($kodeJawaban, 'MINAT_')) {
                continue;
            }

            $minatCode = str_replace('MINAT_', '', $kodeJawaban);
            $kodeBidang = $this->minatToBidangMapping[$minatCode] ?? null;

            if (!$kodeBidang) {
                continue;
            }

            // Gunakan nilai dari opsi jawaban sebagai skor
            $nilai = $jawaban->opsiJawaban->nilai ?? 0;
            
            if (!isset($skorPerBidang[$kodeBidang])) {
                $skorPerBidang[$kodeBidang] = 0;
            }
            $skorPerBidang[$kodeBidang] += $nilai;
        }

        return $skorPerBidang;
    }

    /**
     * Hitung skor asesmen per minat_bidang
     */
    private function calculateSkorAsesmen(Collection $jawabanAsesmen): array
    {
        $skorPerBidang = [];

        foreach ($jawabanAsesmen as $jawaban) {
            $kodePertanyaan = $jawaban->opsiJawaban->pertanyaan->kode_pertanyaan ?? '';
            
            // Extract minat code dari kode_pertanyaan (e.g., "WEB" dari "ASESMEN_WEB_01")
            if (!str_starts_with($kodePertanyaan, 'ASESMEN_')) {
                continue;
            }

            // Hapus "ASESMEN_" dan ambil bagian sebelum "_"
            $parts = explode('_', str_replace('ASESMEN_', '', $kodePertanyaan));
            $minatCode = $parts[0] ?? null;

            if (!$minatCode) {
                continue;
            }

            $kodeBidang = $this->minatToBidangMapping[$minatCode] ?? null;

            if (!$kodeBidang) {
                continue;
            }

            // Gunakan nilai dari opsi jawaban (skala Likert 1-5)
            $nilai = $jawaban->opsiJawaban->nilai ?? 0;
            
            if (!isset($skorPerBidang[$kodeBidang])) {
                $skorPerBidang[$kodeBidang] = 0;
            }
            $skorPerBidang[$kodeBidang] += $nilai;
        }

        return $skorPerBidang;
    }

    /**
     * Konversi nilai huruf A-E ke skala 1-5 (A=5, E=1) untuk digunakan lintas service
     */
    public function convertNilaiHurufKeSkor(string $nilai): int
    {
        $mapping = [
            'A' => 5,
            'B' => 4,
            'C' => 3,
            'D' => 2,
            'E' => 1,
        ];

        return $mapping[strtoupper($nilai)] ?? 1;
    }
}

