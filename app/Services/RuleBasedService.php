<?php

namespace App\Services;

use App\Models\AreaRiset;
use App\Models\Konsultasi;
use App\Models\Rule;
use App\Models\MinatBidang;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class RuleBasedService
{
    public function __construct(
        protected ScoringService $scoringService,
    ) {
    }

    /**
     * Evaluasi konsultasi menggunakan rule-based engine baru berbasis skor.
     */
    public function evaluate(Konsultasi $konsultasi): ?AreaRiset
    {
        // 1. Build context dari jawaban konsultasi (minat, arketipe, skill per minat)
        $jawabanKonsultasi = $konsultasi->jawabanKonsultasis()
            ->with(['opsiJawaban.pertanyaan.kategori'])
            ->get();

        $jawabanMap = $this->buildJawabanMap($jawabanKonsultasi);
        $context = $this->buildContext($jawabanMap);

        // 2. Hitung skor & skill per minat (berdasarkan pertanyaan ASESMEN_...)
        $perBidang = $this->resolveBidangScoresAndSkills($jawabanMap);
        $scoresPerBidang = $perBidang['scores'];
        $skillsPerBidang = $perBidang['skills'];

        // 3. Tentukan minat/bidang dominan (berdasarkan skor asesmen tertinggi)
        $dominantBidang = null;
        $dominantScore = null;

        if (!empty($scoresPerBidang)) {
            arsort($scoresPerBidang);
            $dominantBidang = array_key_first($scoresPerBidang);
            $dominantScore = $scoresPerBidang[$dominantBidang];
        }

        // Jika tidak ada data asesmen per-bidang, fallback ke skor total lama
        if ($dominantBidang === null) {
            $totalSkorMinat = $this->scoringService->calculateHasilMinat($konsultasi);
            $dominantScore = (int) $totalSkorMinat;
        }

        // Skill level: gunakan skill per-bidang dominan jika ada, kalau tidak fallback ke skill global lama
        if ($dominantBidang !== null && isset($skillsPerBidang[$dominantBidang])) {
            $skillLevel = $skillsPerBidang[$dominantBidang];
        } else {
            $skillLevel = $this->resolveGlobalSkillLevel($context['skill_levels'] ?? []);
        }

        $archetype = $context['global_archetype'] ?? 'GENERAL';

        // 4. Jika ada bidang dominan, batasi rules ke minat_bidang tersebut
        $minatBidang = null;
        if ($dominantBidang !== null) {
            $minatBidang = MinatBidang::where('kode_bidang', $dominantBidang)->first();
        }

        $rulesQuery = Rule::active()
            ->orderedByPriority()
            ->with('areaRiset')
            ->where('engine_type', 'rule_based')
            ->where('min_score', '<=', (int) $dominantScore)
            ->where('max_score', '>=', (int) $dominantScore)
            ->where('min_skill_level', '<=', $skillLevel)
            ->where(function ($query) use ($archetype) {
                $query->whereNull('allowed_archetypes')
                    ->orWhereJsonContains('allowed_archetypes', $archetype);
            });

        if ($minatBidang) {
            $rulesQuery->where('minat_bidang_id', $minatBidang->id);
        }

        $rules = $rulesQuery->get();

        if ($rules->isEmpty()) {
            return null;
        }

        // 5. Hitung skor efektif per area_riset berdasarkan skor_boost & prioritas
        $areaScores = [];

        foreach ($rules as $rule) {
            if (!$rule->area_riset_id) {
                continue;
            }

            $boost = $rule->aksi['skor_boost'] ?? 10;
            // Prioritas lebih kecil = lebih penting
            $priorityBonus = max(0, 20 - (int) $rule->prioritas);

            $areaScores[$rule->area_riset_id] = ($areaScores[$rule->area_riset_id] ?? 0)
                + $boost
                + $priorityBonus;
        }

        if (empty($areaScores)) {
            return null;
        }

        arsort($areaScores);
        $areaId = array_key_first($areaScores);

        return AreaRiset::find($areaId);
    }

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

    private function buildContext(array $jawabanMap): array
    {
        return [
            'selected_minat' => $this->resolveMinat($jawabanMap),
            'global_archetype' => $this->resolveArchetype($jawabanMap),
            'discriminator' => $this->resolveDiscriminator($jawabanMap),
            'skill_levels' => $this->resolveSkillLevels($jawabanMap),
        ];
    }

    /**
     * Hitung satu angka skill level global user dari skill per minat.
     * Saat ini menggunakan nilai maksimum agar rule level tinggi hanya match
     * jika user punya kemampuan cukup di salah satu minat.
     */
    private function resolveGlobalSkillLevel(array $skillLevelsPerMinat): int
    {
        if (empty($skillLevelsPerMinat)) {
            return 1;
        }

        $max = max($skillLevelsPerMinat);

        // Normalisasi ke skala 1-4
        return (int) max(1, min(4, round($max)));
    }

    private function resolveArchetype(array $jawabanMap): ?string
    {
        $jawaban = $jawabanMap['ARKETIPE_01'] ?? null;
        if (!$jawaban) {
            return null;
        }

        foreach ($jawaban as $kode => $nilai) {
            if (Str::startsWith($kode, 'ARKETIPE_')) {
                return str_replace('ARKETIPE_', '', $kode);
            }
        }

        return null;
    }

    private function resolveMinat(array $jawabanMap): ?string
    {
        foreach ($jawabanMap as $kodePertanyaan => $jawaban) {
            if (!Str::startsWith($kodePertanyaan, 'MINAT_')) {
                continue;
            }

            foreach ($jawaban as $kodeJawaban => $nilai) {
                if (Str::startsWith($kodeJawaban, 'MINAT_')) {
                    return str_replace('MINAT_', '', $kodeJawaban);
                }
            }
        }

        return null;
    }

    private function resolveDiscriminator(array $jawabanMap): array
    {
        $preferences = [];

        foreach ($jawabanMap as $kodePertanyaan => $jawaban) {
            if (!Str::startsWith($kodePertanyaan, 'DISK_')) {
                continue;
            }

            $parts = explode('_', $kodePertanyaan);
            $minatKode = $parts[1] ?? null;
            if (!$minatKode) {
                continue;
            }

            foreach ($jawaban as $kodeJawaban => $nilai) {
                if (Str::startsWith($kodeJawaban, 'DISK_')) {
                    $preferences[$minatKode] = str_replace('DISK_', '', $kodeJawaban);
                }
            }
        }

        return $preferences;
    }

    private function resolveSkillLevels(array $jawabanMap): array
    {
        $skill = [];

        foreach ($jawabanMap as $kodePertanyaan => $jawaban) {
            if (!Str::startsWith($kodePertanyaan, 'ASESMEN_')) {
                continue;
            }

            $parts = explode('_', $kodePertanyaan);
            $minatKode = $parts[1] ?? null;
            if (!$minatKode) {
                continue;
            }

            $nilai = $jawaban['_max_nilai'] ?? null;
            if ($nilai === null) {
                continue;
            }

            $skill[$minatKode][] = $nilai;
        }

        return array_map(function ($nilaiList) {
            if (empty($nilaiList)) {
                return 0;
            }
            return array_sum($nilaiList) / count($nilaiList);
        }, $skill);
    }

    /**
     * Hitung skor total & skill level per-bidang (per minat) dari pertanyaan ASESMEN_...
     * - skor: jumlah nilai Likert (mis. 1–5) untuk semua pertanyaan ASESMEN di bidang tersebut
     * - skill: rata-rata nilai, dinormalisasi ke skala 1–4 agar sejalan dengan kolom min_skill_level
     */
    private function resolveBidangScoresAndSkills(array $jawabanMap): array
    {
        $perBidangNilai = [];

        foreach ($jawabanMap as $kodePertanyaan => $jawaban) {
            if (!Str::startsWith($kodePertanyaan, 'ASESMEN_')) {
                continue;
            }

            $parts = explode('_', $kodePertanyaan);
            $minatKode = $parts[1] ?? null;
            if (!$minatKode) {
                continue;
            }

            $nilai = $jawaban['_max_nilai'] ?? null;
            if ($nilai === null) {
                continue;
            }

            $perBidangNilai[$minatKode][] = $nilai;
        }

        $scores = [];
        $skills = [];

        foreach ($perBidangNilai as $kodeBidang => $nilaiList) {
            if (empty($nilaiList)) {
                continue;
            }

            $total = array_sum($nilaiList);
            $avg = $total / count($nilaiList);

            $scores[$kodeBidang] = $total;
            // Normalisasi skill ke skala 1-4, supaya cocok dengan kolom min_skill_level
            $skills[$kodeBidang] = (int) max(1, min(4, round($avg)));
        }

        return [
            'scores' => $scores,
            'skills' => $skills,
        ];
    }

    // Legacy matrix / kondisi-based methods telah dihapus karena digantikan
    // oleh engine berbasis skor & kolom eksplisit.
}
