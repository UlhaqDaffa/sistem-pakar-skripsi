<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Rule;
use App\Models\MinatBidang;
use App\Models\AreaRiset;

class RulesSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Rule::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $minatBidang = MinatBidang::all()->keyBy('kode_bidang');
        $areaRisets = AreaRiset::with('minatBidangs')->get();

        $priority = 10;
        foreach ($areaRisets as $area) {
            foreach ($area->minatBidangs as $bidang) {
                $this->createMatrixRule(
                    $area,
                    $bidang,
                    [
                        'skill_threshold' => $this->skillThreshold($area->level_kesulitan),
                        'archetypes' => $area->target_arketipe === 'GENERAL'
                            ? ['CREATOR', 'ANALIS', 'ARCHITECT', 'GENERAL']
                            : [$area->target_arketipe, 'GENERAL'],
                        'prefer_discriminator' => true,
                    ],
                    $priority
                );
                $priority++;
            }
        }

        foreach ($minatBidang as $kode => $bidang) {
            $fallbackArea = AreaRiset::whereHas('minatBidangs', function ($query) use ($bidang) {
                $query->where('minat_bidang.id', $bidang->id);
            })->orderBy('level_kesulitan')->first();

            if ($fallbackArea) {
                $this->createMatrixRule(
                    $fallbackArea,
                    $bidang,
                    [
                        'skill_threshold' => 1,
                        'archetypes' => ['CREATOR', 'ANALIS', 'ARCHITECT', 'GENERAL'],
                        'prefer_low_skill' => true,
                        'fallback' => true,
                    ],
                    $priority += 5,
                    5
                );
            }
        }
    }

    private function createMatrixRule(AreaRiset $area, MinatBidang $minatBidang, array $matrixConfig, int $priority, int $scoreBoost = 20): void
    {
        Rule::create([
            'kode_rule' => 'RULE_' . $area->kode_area . '_' . $minatBidang->kode_bidang . '_' . $priority,
            'nama_rule' => 'Matrix ' . $area->nama_area . ' untuk ' . $minatBidang->nama_bidang,
            'deskripsi' => 'Rule matriks minat, arketipe, dan level skill untuk merekomendasikan ' . $area->nama_area,
            'kondisi' => [
                'matrix' => [
                    'minat' => $minatBidang->kode_bidang,
                    'archetypes' => $matrixConfig['archetypes'] ?? ['GENERAL'],
                    'skill_threshold' => $matrixConfig['skill_threshold'] ?? 2,
                    'prefer_discriminator' => $matrixConfig['prefer_discriminator'] ?? false,
                    'prefer_low_skill' => $matrixConfig['prefer_low_skill'] ?? false,
                    'fallback' => $matrixConfig['fallback'] ?? false,
                ],
            ],
            'aksi' => [
                'area_riset_id' => $area->id,
                'skor_boost' => $scoreBoost,
            ],
            'minat_bidang_id' => $minatBidang->id,
            'area_riset_id' => $area->id,
            'prioritas' => $priority,
            'is_active' => true,
        ]);
    }

    private function skillThreshold(int $level): int
    {
        return match ($level) {
            1 => 2,
            2 => 3,
            default => 4,
        };
    }
}
