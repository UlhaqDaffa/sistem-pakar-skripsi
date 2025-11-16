<?php

namespace App\Services;

use App\Models\AreaRiset;
use App\Models\Konsultasi;
use App\Models\KonfigurasiPembobotan;
use App\Models\MinatBidang;
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
     * Hitung Hasil_Minat berdasarkan jawaban konsultasi
     */
    public function calculateHasilMinat(Konsultasi $konsultasi): ?AreaRiset
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

        // 5. Ambil konfigurasi pembobotan aktif
        $konfigurasi = KonfigurasiPembobotan::getActive();
        if (!$konfigurasi) {
            // Default jika tidak ada konfigurasi
            $bobotMinat = 0.60;
            $bobotAsesmen = 0.40;
        } else {
            $bobotMinat = (float) $konfigurasi->bobot_minat;
            $bobotAsesmen = (float) $konfigurasi->bobot_asesmen;
        }

        // 6. Gabungkan skor dengan pembobotan
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

        // 7. Pilih minat_bidang dengan skor tertinggi
        if (empty($skorFinalPerBidang)) {
            return null;
        }

        arsort($skorFinalPerBidang);
        $kodeBidangTerpilih = array_key_first($skorFinalPerBidang);
        $minatBidang = MinatBidang::where('kode_bidang', $kodeBidangTerpilih)->first();

        if (!$minatBidang) {
            return null;
        }

        // 8. Cari area_riset terbaik di dalam minat_bidang tersebut
        // Untuk sekarang, ambil yang pertama. Bisa dikembangkan dengan logika prioritas
        $areaRiset = $minatBidang->areaRisets()->first();

        return $areaRiset;
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
}

