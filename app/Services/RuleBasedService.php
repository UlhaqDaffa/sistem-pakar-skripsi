<?php

namespace App\Services;

use App\Models\AreaRiset;
use App\Models\Konsultasi;
use App\Models\MinatBidang;
use App\Models\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class RuleBasedService
{
    public function evaluate(Konsultasi $konsultasi): ?AreaRiset
    {
        $jawabanKonsultasi = $konsultasi->jawabanKonsultasis()
            ->with(['opsiJawaban.pertanyaan.kategori'])
            ->get();

        $jawabanMap = $this->buildJawabanMap($jawabanKonsultasi);
        $context = $this->buildContext($jawabanMap);

        $rules = Rule::active()
            ->orderedByPriority()
            ->with(['areaRiset'])
            ->get();

        $areaScores = [];
        $minatScores = [];

        foreach ($rules as $rule) {
            $matrix = $rule->kondisi['matrix'] ?? null;

            if ($matrix) {
                if ($this->matchMatrixRule($matrix, $context)) {
                    $score = $this->scoreMatrixRule($matrix, $context, $rule);
                    $areaScores[$rule->area_riset_id] = ($areaScores[$rule->area_riset_id] ?? 0) + $score;
                }
                continue;
            }

            if ($this->evaluateLegacyRule($rule, $jawabanMap)) {
                $this->executeAction($rule, $areaScores, $minatScores);
            }
        }

        if (!empty($areaScores)) {
            arsort($areaScores);
            $areaId = array_key_first($areaScores);
            return AreaRiset::find($areaId);
        }

        if (!empty($minatScores)) {
            arsort($minatScores);
            $minatId = array_key_first($minatScores);
            $minatBidang = MinatBidang::find($minatId);
            if ($minatBidang) {
                return $this->selectAreaByMinat($minatBidang->kode_bidang, $context);
            }
        }

        return $this->fallbackArea($context);
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

    private function matchMatrixRule(array $matrix, array $context): bool
    {
        $minat = $matrix['minat'] ?? null;
        if ($minat) {
            if (!$context['selected_minat'] || $minat !== $context['selected_minat']) {
                return false;
            }
        }

        $archetypes = $matrix['archetypes'] ?? ['GENERAL'];
        $preferred = $context['discriminator'][$minat] ?? $context['global_archetype'] ?? 'GENERAL';

        if (!in_array($preferred, $archetypes, true)) {
            return false;
        }

        $skillThreshold = $matrix['skill_threshold'] ?? 2;
        $skillActual = $context['skill_levels'][$minat] ?? 0;

        if ($matrix['prefer_low_skill'] ?? false) {
            return $skillActual <= max(2, $skillThreshold);
        }

        return $skillActual >= $skillThreshold;
    }

    private function scoreMatrixRule(array $matrix, array $context, Rule $rule): int
    {
        $minat = $matrix['minat'] ?? $context['selected_minat'];
        $skillActual = $context['skill_levels'][$minat] ?? 0;
        $threshold = $matrix['skill_threshold'] ?? 2;
        $preferred = $context['discriminator'][$minat] ?? $context['global_archetype'] ?? 'GENERAL';

        $bonus = max(0, ($skillActual - $threshold) * 2);
        $archetypeBonus = in_array($preferred, $matrix['archetypes'] ?? [], true) ? 3 : 0;

        return ($rule->aksi['skor_boost'] ?? 10) + $bonus + $archetypeBonus;
    }

    private function evaluateLegacyRule(Rule $rule, array $jawabanMap): bool
    {
        $kondisi = $rule->kondisi;

        if (!isset($kondisi['conditions']) || !is_array($kondisi['conditions'])) {
            return false;
        }

        foreach ($kondisi['conditions'] as $condition) {
            if (!$this->evaluateCondition($condition, $jawabanMap)) {
                return false;
            }
        }

        return true;
    }

    private function evaluateCondition(array $condition, array $jawabanMap): bool
    {
        $kodePertanyaan = $condition['pertanyaan'] ?? null;
        $operator = $condition['operator'] ?? '==';

        if (!$kodePertanyaan || !isset($jawabanMap[$kodePertanyaan])) {
            return false;
        }

        $pertanyaanJawaban = $jawabanMap[$kodePertanyaan];

        if (isset($condition['jawaban'])) {
            $kodeJawaban = $condition['jawaban'];
            return isset($pertanyaanJawaban[$kodeJawaban]);
        }

        if (isset($condition['nilai'])) {
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

    private function executeAction(Rule $rule, array &$skorAreaRiset, array &$skorMinatBidang): void
    {
        $aksi = $rule->aksi;

        if (isset($aksi['area_riset_id'])) {
            $areaRisetId = $aksi['area_riset_id'];
            $skorBoost = $aksi['skor_boost'] ?? 10;
            $skorAreaRiset[$areaRisetId] = ($skorAreaRiset[$areaRisetId] ?? 0) + $skorBoost;
        }

        if (isset($aksi['minat_bidang_id'])) {
            $minatBidangId = $aksi['minat_bidang_id'];
            $skorBoost = $aksi['skor_boost'] ?? 5;
            $skorMinatBidang[$minatBidangId] = ($skorMinatBidang[$minatBidangId] ?? 0) + $skorBoost;
        }

        if ($rule->area_riset_id) {
            $skorAreaRiset[$rule->area_riset_id] = ($skorAreaRiset[$rule->area_riset_id] ?? 0) + 15;
        }

        if ($rule->minat_bidang_id) {
            $skorMinatBidang[$rule->minat_bidang_id] = ($skorMinatBidang[$rule->minat_bidang_id] ?? 0) + 10;
        }
    }

    private function selectAreaByMinat(string $kodeMinat, array $context): ?AreaRiset
    {
        $skill = $context['skill_levels'][$kodeMinat] ?? 0;
        $preferredArchetype = $context['discriminator'][$kodeMinat] ?? $context['global_archetype'] ?? 'GENERAL';

        $areas = AreaRiset::whereHas('minatBidangs', function ($query) use ($kodeMinat) {
            $query->where('kode_bidang', $kodeMinat);
        })->orderBy('level_kesulitan')->get();

        if ($areas->isEmpty()) {
            return null;
        }

        $matched = $areas->first(function (AreaRiset $area) use ($skill, $preferredArchetype) {
            return $area->level_kesulitan <= max(1, round($skill)) &&
                ($area->target_arketipe === $preferredArchetype || $area->target_arketipe === 'GENERAL');
        });

        return $matched ?: $areas->first();
    }

    private function fallbackArea(array $context): ?AreaRiset
    {
        $minat = $context['selected_minat'];
        if (!$minat) {
            return null;
        }

        return $this->selectAreaByMinat($minat, $context);
    }
}
