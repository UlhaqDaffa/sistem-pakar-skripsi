<?php

namespace App\Services;

use App\Models\AreaRiset;
use App\Models\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class DecisionTreeProcessor
{
    /**
     * Pilih AreaRiset spesifik berdasarkan hasil API Decision Tree dan nilai mata kuliah.
     *
     * @param string $kodeAreaApi  Kode bidang dari API (AI, DATA, dll)
     * @param array  $nilai        Nilai mata kuliah skala 1-5
     * @param float|null $confidence  Nilai confidence dari API (opsional)
     */
    public function selectAreaRiset(string $kodeAreaApi, array $nilai, ?float $confidence = null): ?AreaRiset
    {
        $kodeAreaApi = strtoupper($kodeAreaApi);
        $skillScore = $this->computeSkillScore($kodeAreaApi, $nilai);
        $level = $this->mapSkillToLevel($skillScore);

        $rule = Rule::active()
            ->where('engine_type', 'decision_tree')
            ->where('dt_config->bidang_kode', $kodeAreaApi)
            ->where(function ($query) use ($level) {
                $query->where('dt_config->min_level', '<=', $level)
                    ->where('dt_config->max_level', '>=', $level);
            })
            ->with('areaRiset')
            ->orderedByPriority()
            ->first();

        if ($rule && $rule->areaRiset) {
            Log::info('DecisionTreeProcessor: Rule decision tree ditemukan', [
                'kode_area_api' => $kodeAreaApi,
                'skill_score' => $skillScore,
                'level' => $level,
                'rule_id' => $rule->id,
                'area_riset_id' => $rule->area_riset_id,
                'confidence' => $confidence,
            ]);

            return $rule->areaRiset;
        }

        $fallback = $this->fallbackAreaRiset($kodeAreaApi, $level);

        if ($fallback) {
            Log::info('DecisionTreeProcessor: Menggunakan fallback area riset', [
                'kode_area_api' => $kodeAreaApi,
                'skill_score' => $skillScore,
                'level' => $level,
                'area_riset_id' => $fallback->id,
                'confidence' => $confidence,
            ]);
        } else {
            Log::warning('DecisionTreeProcessor: Tidak menemukan area riset untuk hasil API', [
                'kode_area_api' => $kodeAreaApi,
                'skill_score' => $skillScore,
                'level' => $level,
                'confidence' => $confidence,
            ]);
        }

        return $fallback;
    }

    /**
     * Hitung skor kemampuan per bidang.
     */
    private function computeSkillScore(string $kodeAreaApi, array $nilai): float
    {
        $algoritma = (float) ($nilai['algoritma'] ?? 0);
        $pemrograman = (float) ($nilai['pemrograman'] ?? 0);
        $basisData = (float) ($nilai['basis_data'] ?? 0);
        $kecerdasanBuatan = (float) ($nilai['kecerdasan_buatan'] ?? 0);

        return match ($kodeAreaApi) {
            'AI' => (0.7 * $kecerdasanBuatan) + (0.3 * $algoritma),
            'DATA' => ($basisData + $algoritma) / 2,
            'RPL' => ($pemrograman + $algoritma) / 2,
            'PENG' => $pemrograman,
            'CITRA' => ($kecerdasanBuatan + $algoritma) / 2,
            'NLP' => ($kecerdasanBuatan + $pemrograman) / 2,
            'HCI' => $pemrograman,
            'GRAF' => ($pemrograman + $algoritma) / 2,
            'JAR' => ($algoritma + $basisData) / 2,
            'IOT' => ($pemrograman + $algoritma) / 2,
            default => max($algoritma, $pemrograman, $basisData, $kecerdasanBuatan),
        };
    }

    /**
     * Konversi skor kemampuan ke level kesulitan 1-3.
     */
    private function mapSkillToLevel(float $skillScore): int
    {
        if ($skillScore >= 4.5) {
            return 3;
        }

        if ($skillScore >= 3.0) {
            return 2;
        }

        return 1;
    }

    /**
     * Fallback pemilihan AreaRiset menggunakan relasi MinatBidang.
     */
    private function fallbackAreaRiset(string $kodeAreaApi, int $targetLevel): ?AreaRiset
    {
        /** @var Collection<int, AreaRiset> $areas */
        $areas = AreaRiset::whereHas('minatBidangs', function ($query) use ($kodeAreaApi) {
            $query->where('kode_bidang', strtoupper($kodeAreaApi));
        })->get();

        if ($areas->isEmpty()) {
            return null;
        }

        $sorted = $areas->sortBy(function (AreaRiset $area) use ($targetLevel) {
            return abs($area->level_kesulitan - $targetLevel);
        });

        return $sorted->first();
    }
}

