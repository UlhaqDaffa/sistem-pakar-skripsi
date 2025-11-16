<?php

namespace App\Services;

use App\Models\Konsultasi;
use App\Models\Rule;
use App\Models\AreaRiset;
use App\Models\MinatBidang;
use Illuminate\Support\Collection;

class RuleBasedService
{
    /**
     * Evaluasi semua rules dan kembalikan rekomendasi area riset
     * 
     * @param Konsultasi $konsultasi
     * @return AreaRiset|null
     */
    public function evaluate(Konsultasi $konsultasi): ?AreaRiset
    {
        // Ambil semua jawaban konsultasi
        $jawabanKonsultasi = $konsultasi->jawabanKonsultasis()
            ->with(['opsiJawaban.pertanyaan.kategori'])
            ->get();

        // Buat map jawaban untuk evaluasi cepat
        $jawabanMap = $this->buildJawabanMap($jawabanKonsultasi);

        // Ambil semua rules aktif, urutkan berdasarkan prioritas
        $rules = Rule::active()
            ->orderedByPriority()
            ->with(['minatBidang', 'areaRiset'])
            ->get();

        $skorAreaRiset = [];
        $skorMinatBidang = [];

        // Evaluasi setiap rule
        foreach ($rules as $rule) {
            if ($this->evaluateRule($rule, $jawabanMap)) {
                // Rule cocok, eksekusi aksi
                $this->executeAction($rule, $skorAreaRiset, $skorMinatBidang);
            }
        }

        // Pilih area riset dengan skor tertinggi
        if (!empty($skorAreaRiset)) {
            arsort($skorAreaRiset);
            $areaRisetId = array_key_first($skorAreaRiset);
            return AreaRiset::find($areaRisetId);
        }

        // Jika tidak ada area riset spesifik, coba dari minat bidang
        if (!empty($skorMinatBidang)) {
            arsort($skorMinatBidang);
            $minatBidangId = array_key_first($skorMinatBidang);
            $minatBidang = MinatBidang::find($minatBidangId);
            
            if ($minatBidang) {
                // Ambil area riset pertama dari minat bidang tersebut
                return $minatBidang->areaRisets()->first();
            }
        }

        return null;
    }

    /**
     * Build map jawaban untuk evaluasi cepat
     * Format: ['kode_pertanyaan' => ['kode_jawaban' => nilai, ...], ...]
     */
    private function buildJawabanMap(Collection $jawabanKonsultasi): array
    {
        $map = [];

        foreach ($jawabanKonsultasi as $jawaban) {
            $pertanyaan = $jawaban->opsiJawaban->pertanyaan ?? null;
            if (!$pertanyaan) {
                continue;
            }

            $kodePertanyaan = $pertanyaan->kode_pertanyaan;
            $kodeJawaban = $jawaban->opsiJawaban->kode_jawaban;
            $nilai = $jawaban->opsiJawaban->nilai ?? 0;

            if (!isset($map[$kodePertanyaan])) {
                $map[$kodePertanyaan] = [];
            }

            $map[$kodePertanyaan][$kodeJawaban] = $nilai;
            $map[$kodePertanyaan]['_max_nilai'] = max($map[$kodePertanyaan]['_max_nilai'] ?? 0, $nilai);
            $map[$kodePertanyaan]['_min_nilai'] = min($map[$kodePertanyaan]['_min_nilai'] ?? PHP_INT_MAX, $nilai);
        }

        return $map;
    }

    /**
     * Evaluasi apakah rule cocok dengan kondisi
     */
    private function evaluateRule(Rule $rule, array $jawabanMap): bool
    {
        $kondisi = $rule->kondisi;

        if (!isset($kondisi['conditions']) || !is_array($kondisi['conditions'])) {
            return false;
        }

        // Semua kondisi harus terpenuhi (AND logic)
        foreach ($kondisi['conditions'] as $condition) {
            if (!$this->evaluateCondition($condition, $jawabanMap)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Evaluasi satu kondisi
     */
    private function evaluateCondition(array $condition, array $jawabanMap): bool
    {
        $kodePertanyaan = $condition['pertanyaan'] ?? null;
        $operator = $condition['operator'] ?? '==';
        
        if (!$kodePertanyaan || !isset($jawabanMap[$kodePertanyaan])) {
            return false;
        }

        $pertanyaanJawaban = $jawabanMap[$kodePertanyaan];

        // Evaluasi berdasarkan tipe kondisi
        if (isset($condition['jawaban'])) {
            // Kondisi: jawaban spesifik
            $kodeJawaban = $condition['jawaban'];
            return isset($pertanyaanJawaban[$kodeJawaban]);
        } elseif (isset($condition['nilai'])) {
            // Kondisi: nilai/nilai minimum
            $targetNilai = (int) $condition['nilai'];
            $maxNilai = $pertanyaanJawaban['_max_nilai'] ?? 0;

            return match ($operator) {
                '>=' => $maxNilai >= $targetNilai,
                '>' => $maxNilai > $targetNilai,
                '<=' => $maxNilai <= $targetNilai,
                '<' => $maxNilai < $targetNilai,
                '==' => $maxNilai == $targetNilai,
                default => false,
            };
        }

        return false;
    }

    /**
     * Eksekusi aksi dari rule
     */
    private function executeAction(Rule $rule, array &$skorAreaRiset, array &$skorMinatBidang): void
    {
        $aksi = $rule->aksi;

        // Tambah skor area riset
        if (isset($aksi['area_riset_id'])) {
            $areaRisetId = $aksi['area_riset_id'];
            $skorBoost = $aksi['skor_boost'] ?? 10;
            $skorAreaRiset[$areaRisetId] = ($skorAreaRiset[$areaRisetId] ?? 0) + $skorBoost;
        }

        // Tambah skor minat bidang
        if (isset($aksi['minat_bidang_id'])) {
            $minatBidangId = $aksi['minat_bidang_id'];
            $skorBoost = $aksi['skor_boost'] ?? 5;
            $skorMinatBidang[$minatBidangId] = ($skorMinatBidang[$minatBidangId] ?? 0) + $skorBoost;
        }

        // Jika rule punya relasi langsung
        if ($rule->area_riset_id) {
            $skorAreaRiset[$rule->area_riset_id] = ($skorAreaRiset[$rule->area_riset_id] ?? 0) + 15;
        }

        if ($rule->minat_bidang_id) {
            $skorMinatBidang[$rule->minat_bidang_id] = ($skorMinatBidang[$rule->minat_bidang_id] ?? 0) + 10;
        }
    }
}

