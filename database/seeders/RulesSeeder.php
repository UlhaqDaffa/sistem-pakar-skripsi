<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Rule;
use App\Models\MinatBidang;
use App\Models\AreaRiset;
use Database\Seeders\MinatBidangSeeder;
use Database\Seeders\AreaRisetSeeder;

class RulesSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Rule::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Pastikan data referensi sudah ada ketika RulesSeeder dijalankan secara terpisah
        // (misalnya: php artisan db:seed --class="Database\\Seeders\\RulesSeeder")
        if (MinatBidang::count() === 0 || AreaRiset::count() === 0) {
            $this->call([
                MinatBidangSeeder::class,
                AreaRisetSeeder::class,
            ]);
        }

        $this->createRules();
    }

    /**
     * Buat rules berbasis rentang skor & level skill untuk setiap kombinasi AreaRiset x MinatBidang.
     */
    public function createRules(): void
    {
        $minatBidang = MinatBidang::all()->keyBy('kode_bidang');
        $areaRisets = AreaRiset::with('minatBidangs')->get();

        $priority = 10;

        foreach ($areaRisets as $area) {
            [$minScore, $maxScore] = $this->scoreRangeForLevel($area->level_kesulitan);
            $minSkillLevel = $this->minSkillForLevel($area->level_kesulitan);
            $allowedArchetypes = $this->allowedArchetypesForArea($area);

            foreach ($area->minatBidangs as $bidang) {
                Rule::create([
                    'kode_rule' => 'RULE_' . $area->kode_area . '_' . $bidang->kode_bidang . '_' . $priority,
                    'nama_rule' => 'Rule ' . $area->nama_area . ' untuk ' . $bidang->nama_bidang,
                    'deskripsi' => 'Rule rentang skor & skill untuk merekomendasikan ' . $area->nama_area,
                    'aksi' => [
                        'area_riset_id' => $area->id,
                        'skor_boost' => 20,
                    ],
                    'min_score' => $minScore,
                    'max_score' => $maxScore,
                    'min_skill_level' => $minSkillLevel,
                    'allowed_archetypes' => $allowedArchetypes,
                    'engine_type' => 'rule_based',
                    'minat_bidang_id' => $bidang->id,
                    'area_riset_id' => $area->id,
                    'prioritas' => $priority,
                    'is_active' => true,
                ]);

                $priority++;
            }
        }

        // Fallback rules per minat_bidang: jangkau semua skor & semua arketipe, prioritas lebih rendah
        foreach ($minatBidang as $bidang) {
            $fallbackArea = AreaRiset::whereHas('minatBidangs', function ($query) use ($bidang) {
                $query->where('minat_bidang.id', $bidang->id);
            })->orderBy('level_kesulitan')->first();

            if (!$fallbackArea) {
                continue;
            }

            Rule::create([
                'kode_rule' => 'RULE_FALLBACK_' . $fallbackArea->kode_area . '_' . $bidang->kode_bidang . '_' . ($priority + 5),
                'nama_rule' => 'Fallback ' . $fallbackArea->nama_area . ' untuk ' . $bidang->nama_bidang,
                'deskripsi' => 'Rule fallback untuk minat ' . $bidang->nama_bidang . ' dengan skor rendah',
                'aksi' => [
                    'area_riset_id' => $fallbackArea->id,
                    'skor_boost' => 5,
                ],
                'min_score' => 0,
                'max_score' => 1000,
                'min_skill_level' => 1,
                'allowed_archetypes' => ['CREATOR', 'ANALIS', 'ARCHITECT', 'GENERAL'],
                'engine_type' => 'rule_based',
                'minat_bidang_id' => $bidang->id,
                'area_riset_id' => $fallbackArea->id,
                'prioritas' => $priority + 5,
                'is_active' => true,
            ]);

            $priority += 5;
        }
    }

    /**
     * Mapping level kesulitan ke rentang skor.
     */
    private function scoreRangeForLevel(int $level): array
    {
        /**
         * Saat ini setiap bidang punya 5 pertanyaan ASESMEN dengan skala Likert 1–5,
         * sehingga skor total per-bidang berada di kisaran 5–25.
         *
         * Kita bagi menjadi:
         * - Level 1 (mudah):   skor rendah   →  0–11
         * - Level 2 (sedang):  skor sedang  → 12–18
         * - Level 3 (sulit):   skor tinggi  → 19–30
         *
         * Rentang ini cukup lebar untuk membedakan user yang banyak menjawab
         * "tidak paham" vs "sangat paham".
         */
        return match ($level) {
            1 => [0, 11],
            2 => [12, 18],
            default => [19, 30],
        };
    }

    /**
     * Mapping level kesulitan ke minimal skill level.
     */
    private function minSkillForLevel(int $level): int
    {
        /**
         * Skill level di-normalisasi ke skala 1–4 di RuleBasedService:
         * - 1 = sangat kurang
         * - 2 = kurang / cukup
         * - 3 = baik
         * - 4 = sangat baik
         *
         * Mapping di bawah ini membuat:
         * - Level 1 bisa direkomendasikan mulai dari skill 1 (pengguna pemula)
         * - Level 2 butuh skill minimal 2
         * - Level 3 butuh skill minimal 3
         */
        return match ($level) {
            1 => 1,
            2 => 2,
            default => 3,
        };
    }

    /**
     * Arketipe yang diizinkan untuk sebuah area riset.
     */
    private function allowedArchetypesForArea(AreaRiset $area): array
    {
        if ($area->target_arketipe === 'GENERAL' || !$area->target_arketipe) {
            return ['CREATOR', 'ANALIS', 'ARCHITECT', 'GENERAL'];
        }

        return [$area->target_arketipe, 'GENERAL'];
    }
}