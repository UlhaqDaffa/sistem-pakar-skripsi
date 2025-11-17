<?php

namespace App\Services;

use App\Models\AreaRiset;
use App\Models\MinatBidang;
use Illuminate\Support\Facades\Log;

class RekonsiliasiService
{
    /**
     * Rekonsiliasi Hasil_Minat dan Hasil_Akademik
     *
     * Logika:
     * 1. Jika Hasil_Akademik null, return Hasil_Minat
     * 2. Cek apakah Hasil_Akademik berada di dalam minat_bidang dari Hasil_Minat
     * 3. Jika Cocok: return Hasil_Akademik (prioritaskan hasil akademik yang sesuai minat)
     * 4. Jika Tidak Cocok: cari area_riset terbaik di dalam minat_bidang Hasil_Minat
     *
     * @param AreaRiset $hasilMinat
     * @param AreaRiset|null $hasilAkademik
     * @return AreaRiset
     */
    public function rekonsiliasi(AreaRiset $hasilMinat, ?AreaRiset $hasilAkademik): AreaRiset
    {
        // Jika tidak ada hasil akademik, langsung return hasil minat
        if (!$hasilAkademik) {
            Log::info('Rekonsiliasi: Tidak ada Hasil_Akademik, menggunakan Hasil_Minat', [
                'hasil_minat_id' => $hasilMinat->id,
                'hasil_minat_kode' => $hasilMinat->kode_area
            ]);
            return $hasilMinat;
        }

        // Ambil minat_bidang dari Hasil_Minat
        $minatBidangsHasilMinat = $hasilMinat->minatBidangs;

        if ($minatBidangsHasilMinat->isEmpty()) {
            Log::warning('Rekonsiliasi: Hasil_Minat tidak memiliki minat_bidang', [
                'hasil_minat_id' => $hasilMinat->id
            ]);
            return $hasilMinat;
        }

        // Ambil minat_bidang dari Hasil_Akademik
        $minatBidangsHasilAkademik = $hasilAkademik->minatBidangs;

        // Cek apakah ada irisan antara minat_bidang Hasil_Minat dan Hasil_Akademik
        $minatBidangIdsHasilMinat = $minatBidangsHasilMinat->pluck('id')->toArray();
        $minatBidangIdsHasilAkademik = $minatBidangsHasilAkademik->pluck('id')->toArray();

        $intersection = array_intersect($minatBidangIdsHasilMinat, $minatBidangIdsHasilAkademik);

        // Jika ada irisan (Cocok)
        if (!empty($intersection)) {
            Log::info('Rekonsiliasi: Hasil_Akademik cocok dengan Hasil_Minat', [
                'hasil_minat_id' => $hasilMinat->id,
                'hasil_akademik_id' => $hasilAkademik->id,
                'minat_bidang_intersection' => $intersection
            ]);
            // Prioritaskan Hasil_Akademik karena sesuai dengan minat pengguna
            return $hasilAkademik;
        }

        // Jika tidak cocok, cari area_riset terbaik di dalam minat_bidang Hasil_Minat
        Log::info('Rekonsiliasi: Hasil_Akademik tidak cocok, mencari area_riset terbaik di minat_bidang Hasil_Minat', [
            'hasil_minat_id' => $hasilMinat->id,
            'hasil_akademik_id' => $hasilAkademik->id,
            'minat_bidang_ids' => $minatBidangIdsHasilMinat
        ]);

        // Ambil semua area_riset yang terkait dengan minat_bidang Hasil_Minat
        $areaRisetOptions = AreaRiset::whereHas('minatBidangs', function ($query) use ($minatBidangIdsHasilMinat) {
            $query->whereIn('minat_bidang.id', $minatBidangIdsHasilMinat);
        })->get();

        if ($areaRisetOptions->isEmpty()) {
            Log::warning('Rekonsiliasi: Tidak ada area_riset di minat_bidang Hasil_Minat', [
                'hasil_minat_id' => $hasilMinat->id
            ]);
            return $hasilMinat;
        }

        // Strategi pemilihan area_riset terbaik:
        // 1. Jika Hasil_Minat ada di options, gunakan Hasil_Minat
        // 2. Jika tidak, pilih yang pertama 
        $bestAreaRiset = $areaRisetOptions->firstWhere('id', $hasilMinat->id);

        if ($bestAreaRiset) {
            Log::info('Rekonsiliasi: Menggunakan Hasil_Minat sebagai area_riset terbaik', [
                'area_riset_id' => $bestAreaRiset->id
            ]);
            return $bestAreaRiset;
        }

        // Gunakan area_riset pertama dari options
        $selectedAreaRiset = $areaRisetOptions->first();

        Log::info('Rekonsiliasi: Memilih area_riset dari minat_bidang Hasil_Minat', [
            'selected_area_riset_id' => $selectedAreaRiset->id,
            'selected_area_riset_kode' => $selectedAreaRiset->kode_area,
            'total_options' => $areaRisetOptions->count()
        ]);

        return $selectedAreaRiset;
    }

    /**
     * Helper method untuk mendapatkan area_riset berdasarkan minat_bidang
     *
     * @param MinatBidang $minatBidang
     * @return AreaRiset|null
     */
    public function getBestAreaRisetByMinatBidang(MinatBidang $minatBidang): ?AreaRiset
    {
        return $minatBidang->areaRisets()->first();
    }
}

