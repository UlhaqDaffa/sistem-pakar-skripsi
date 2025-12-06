<?php

namespace Database\Seeders;

use App\Models\AreaRiset;
use App\Models\MinatBidang;
use App\Models\Rule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DecisionTreeRulesSeeder extends Seeder
{
    /**
     * Kode bidang utama yang digunakan decision tree API.
     */
    private array $bidangCodes = [
        'AI',
        'DATA',
        'RPL',
        'PENG',
        'CITRA',
        'NLP',
        'HCI',
        'GRAF',
        'JAR',
        'IOT',
    ];

    /**
     * Jalankan seeder.
     */
    public function run(): void
    {
        $minatBidangs = MinatBidang::with(['areaRisets' => function ($query) {
            $query->orderBy('level_kesulitan');
        }])->get()->keyBy(fn (MinatBidang $minat) => strtoupper($minat->kode_bidang));

        foreach ($this->bidangCodes as $kodeBidang) {
            $minatBidang = $minatBidangs[strtoupper($kodeBidang)] ?? null;
            if (!$minatBidang) {
                continue;
            }

            $areaRisets = $minatBidang->areaRisets;
            if ($areaRisets->isEmpty()) {
                continue;
            }

            for ($level = 1; $level <= 3; $level++) {
                $areaRiset = $this->findAreaByLevel($areaRisets, $level);
                if (!$areaRiset) {
                    continue;
                }

                Rule::updateOrCreate(
                    ['kode_rule' => "DT_{$kodeBidang}_LEVEL_{$level}"],
                    [
                        'nama_rule' => "Decision Tree {$kodeBidang} - Level {$level}",
                        'deskripsi' => "Mapping hasil {$kodeBidang} dengan level {$level} ke area riset spesifik",
                        'aksi' => [
                            'engine' => 'decision_tree',
                        ],
                        'dt_config' => [
                            'bidang_kode' => strtoupper($kodeBidang),
                            'min_level' => $level,
                            'max_level' => $level,
                        ],
                        'engine_type' => 'decision_tree',
                        'min_score' => 0,
                        'max_score' => 1000,
                        'min_skill_level' => 1,
                        'allowed_archetypes' => null,
                        'minat_bidang_id' => $minatBidang->id,
                        'area_riset_id' => $areaRiset->id,
                        'prioritas' => 10 + $level,
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    /**
     * Pilih AreaRiset yang cocok dengan level, atau level terdekat jika tidak tersedia.
     */
    private function findAreaByLevel(Collection $areas, int $targetLevel): ?AreaRiset
    {
        $exact = $areas->firstWhere('level_kesulitan', $targetLevel);
        if ($exact) {
            return $exact;
        }

        return $areas
            ->sortBy(fn (AreaRiset $area) => abs($area->level_kesulitan - $targetLevel))
            ->first();
    }
}

