<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rule;
use App\Models\MinatBidang;
use App\Models\AreaRiset;
use Illuminate\Support\Facades\DB;

class RulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Rule::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get Minat Bidang
        $minatBidangs = MinatBidang::all()->keyBy('kode_bidang');
        
        // Get Area Riset untuk setiap minat bidang
        $areaRisets = [];
        foreach ($minatBidangs as $kode => $minat) {
            $areaRisets[$kode] = $minat->areaRisets()->first();
        }

        // Helper function untuk membuat rule
        $createRule = function ($kodeRule, $namaRule, $deskripsi, $kondisi, $aksi, $minatBidang = null, $areaRiset = null, $prioritas = 100) {
            Rule::create([
                'kode_rule' => $kodeRule,
                'nama_rule' => $namaRule,
                'deskripsi' => $deskripsi,
                'kondisi' => $kondisi,
                'aksi' => $aksi,
                'minat_bidang_id' => $minatBidang?->id,
                'area_riset_id' => $areaRiset?->id,
                'prioritas' => $prioritas,
                'is_active' => true,
            ]);
        };

        // === RULES UNTUK SETIAP MINAT BIDANG ===
        
        // Rule 1: Jika minat RPL dan asesmen RPL tinggi (>= 4), boost area riset RPL
        if (isset($minatBidangs['RPL']) && isset($areaRisets['RPL'])) {
            $createRule(
                'RULE_RPL_01',
                'Minat RPL dengan Asesmen Tinggi',
                'Jika user memilih minat RPL dan memiliki asesmen RPL >= 4, boost area riset RPL',
                [
                    'conditions' => [
                        ['pertanyaan' => 'MINAT_CREATOR_01', 'jawaban' => 'MINAT_RPL'],
                        ['pertanyaan' => 'ASESMEN_RPL_01', 'nilai' => 4, 'operator' => '>='],
                    ]
                ],
                [
                    'minat_bidang_id' => $minatBidangs['RPL']->id,
                    'area_riset_id' => $areaRisets['RPL']?->id,
                    'skor_boost' => 20,
                ],
                $minatBidangs['RPL'],
                $areaRisets['RPL'],
                10
            );
        }

        // Rule 2: Jika minat PENG dan asesmen PENG tinggi (>= 4), boost area riset PENG
        if (isset($minatBidangs['PENG']) && isset($areaRisets['PENG'])) {
            $createRule(
                'RULE_PENG_01',
                'Minat PENG dengan Asesmen Tinggi',
                'Jika user memilih minat PENG dan memiliki asesmen PENG >= 4, boost area riset PENG',
                [
                    'conditions' => [
                        ['pertanyaan' => 'MINAT_CREATOR_01', 'jawaban' => 'MINAT_PENG'],
                        ['pertanyaan' => 'ASESMEN_PENG_01', 'nilai' => 4, 'operator' => '>='],
                    ]
                ],
                [
                    'minat_bidang_id' => $minatBidangs['PENG']->id,
                    'area_riset_id' => $areaRisets['PENG']?->id,
                    'skor_boost' => 20,
                ],
                $minatBidangs['PENG'],
                $areaRisets['PENG'],
                10
            );
        }

        // Rule 3: Jika minat AI dan asesmen AI tinggi (>= 4), boost area riset AI
        if (isset($minatBidangs['AI']) && isset($areaRisets['AI'])) {
            $createRule(
                'RULE_AI_01',
                'Minat AI dengan Asesmen Tinggi',
                'Jika user memilih minat AI dan memiliki asesmen AI >= 4, boost area riset AI',
                [
                    'conditions' => [
                        ['pertanyaan' => 'MINAT_ANALIS_01', 'jawaban' => 'MINAT_AI'],
                        ['pertanyaan' => 'ASESMEN_AI_01', 'nilai' => 4, 'operator' => '>='],
                    ]
                ],
                [
                    'minat_bidang_id' => $minatBidangs['AI']->id,
                    'area_riset_id' => $areaRisets['AI']?->id,
                    'skor_boost' => 20,
                ],
                $minatBidangs['AI'],
                $areaRisets['AI'],
                10
            );
        }

        // Rule 4: Jika minat DATA dan asesmen DATA tinggi (>= 4), boost area riset DATA
        if (isset($minatBidangs['DATA']) && isset($areaRisets['DATA'])) {
            $createRule(
                'RULE_DATA_01',
                'Minat DATA dengan Asesmen Tinggi',
                'Jika user memilih minat DATA dan memiliki asesmen DATA >= 4, boost area riset DATA',
                [
                    'conditions' => [
                        ['pertanyaan' => 'MINAT_ANALIS_01', 'jawaban' => 'MINAT_DATA'],
                        ['pertanyaan' => 'ASESMEN_DATA_01', 'nilai' => 4, 'operator' => '>='],
                    ]
                ],
                [
                    'minat_bidang_id' => $minatBidangs['DATA']->id,
                    'area_riset_id' => $areaRisets['DATA']?->id,
                    'skor_boost' => 20,
                ],
                $minatBidangs['DATA'],
                $areaRisets['DATA'],
                10
            );
        }

        // Rule 5: Jika minat CITRA dan asesmen CITRA tinggi (>= 4), boost area riset CITRA
        if (isset($minatBidangs['CITRA']) && isset($areaRisets['CITRA'])) {
            $createRule(
                'RULE_CITRA_01',
                'Minat CITRA dengan Asesmen Tinggi',
                'Jika user memilih minat CITRA dan memiliki asesmen CITRA >= 4, boost area riset CITRA',
                [
                    'conditions' => [
                        ['pertanyaan' => 'MINAT_ANALIS_01', 'jawaban' => 'MINAT_CITRA'],
                        ['pertanyaan' => 'ASESMEN_CITRA_01', 'nilai' => 4, 'operator' => '>='],
                    ]
                ],
                [
                    'minat_bidang_id' => $minatBidangs['CITRA']->id,
                    'area_riset_id' => $areaRisets['CITRA']?->id,
                    'skor_boost' => 20,
                ],
                $minatBidangs['CITRA'],
                $areaRisets['CITRA'],
                10
            );
        }

        // Rule 6: Jika minat NLP dan asesmen NLP tinggi (>= 4), boost area riset NLP
        if (isset($minatBidangs['NLP']) && isset($areaRisets['NLP'])) {
            $createRule(
                'RULE_NLP_01',
                'Minat NLP dengan Asesmen Tinggi',
                'Jika user memilih minat NLP dan memiliki asesmen NLP >= 4, boost area riset NLP',
                [
                    'conditions' => [
                        ['pertanyaan' => 'MINAT_ANALIS_01', 'jawaban' => 'MINAT_NLP'],
                        ['pertanyaan' => 'ASESMEN_NLP_01', 'nilai' => 4, 'operator' => '>='],
                    ]
                ],
                [
                    'minat_bidang_id' => $minatBidangs['NLP']->id,
                    'area_riset_id' => $areaRisets['NLP']?->id,
                    'skor_boost' => 20,
                ],
                $minatBidangs['NLP'],
                $areaRisets['NLP'],
                10
            );
        }

        // Rule 7: Jika minat HCI dan asesmen HCI tinggi (>= 4), boost area riset HCI
        if (isset($minatBidangs['HCI']) && isset($areaRisets['HCI'])) {
            $createRule(
                'RULE_HCI_01',
                'Minat HCI dengan Asesmen Tinggi',
                'Jika user memilih minat HCI dan memiliki asesmen HCI >= 4, boost area riset HCI',
                [
                    'conditions' => [
                        ['pertanyaan' => 'MINAT_ARCHITECT_01', 'jawaban' => 'MINAT_HCI'],
                        ['pertanyaan' => 'ASESMEN_HCI_01', 'nilai' => 4, 'operator' => '>='],
                    ]
                ],
                [
                    'minat_bidang_id' => $minatBidangs['HCI']->id,
                    'area_riset_id' => $areaRisets['HCI']?->id,
                    'skor_boost' => 20,
                ],
                $minatBidangs['HCI'],
                $areaRisets['HCI'],
                10
            );
        }

        // Rule 8: Jika minat GRAF dan asesmen GRAF tinggi (>= 4), boost area riset GRAF
        if (isset($minatBidangs['GRAF']) && isset($areaRisets['GRAF'])) {
            $createRule(
                'RULE_GRAF_01',
                'Minat GRAF dengan Asesmen Tinggi',
                'Jika user memilih minat GRAF dan memiliki asesmen GRAF >= 4, boost area riset GRAF',
                [
                    'conditions' => [
                        ['pertanyaan' => 'MINAT_CREATOR_01', 'jawaban' => 'MINAT_GRAF'],
                        ['pertanyaan' => 'ASESMEN_GRAF_01', 'nilai' => 4, 'operator' => '>='],
                    ]
                ],
                [
                    'minat_bidang_id' => $minatBidangs['GRAF']->id,
                    'area_riset_id' => $areaRisets['GRAF']?->id,
                    'skor_boost' => 20,
                ],
                $minatBidangs['GRAF'],
                $areaRisets['GRAF'],
                10
            );
        }

        // Rule 9: Jika minat JAR dan asesmen JAR tinggi (>= 4), boost area riset JAR
        if (isset($minatBidangs['JAR']) && isset($areaRisets['JAR'])) {
            $createRule(
                'RULE_JAR_01',
                'Minat JAR dengan Asesmen Tinggi',
                'Jika user memilih minat JAR dan memiliki asesmen JAR >= 4, boost area riset JAR',
                [
                    'conditions' => [
                        ['pertanyaan' => 'MINAT_ARCHITECT_01', 'jawaban' => 'MINAT_JAR'],
                        ['pertanyaan' => 'ASESMEN_JAR_01', 'nilai' => 4, 'operator' => '>='],
                    ]
                ],
                [
                    'minat_bidang_id' => $minatBidangs['JAR']->id,
                    'area_riset_id' => $areaRisets['JAR']?->id,
                    'skor_boost' => 20,
                ],
                $minatBidangs['JAR'],
                $areaRisets['JAR'],
                10
            );
        }

        // Rule 10: Jika minat IOT dan asesmen IOT tinggi (>= 4), boost area riset IOT
        if (isset($minatBidangs['IOT']) && isset($areaRisets['IOT'])) {
            $createRule(
                'RULE_IOT_01',
                'Minat IOT dengan Asesmen Tinggi',
                'Jika user memilih minat IOT dan memiliki asesmen IOT >= 4, boost area riset IOT',
                [
                    'conditions' => [
                        ['pertanyaan' => 'MINAT_ARCHITECT_01', 'jawaban' => 'MINAT_IOT'],
                        ['pertanyaan' => 'ASESMEN_IOT_01', 'nilai' => 4, 'operator' => '>='],
                    ]
                ],
                [
                    'minat_bidang_id' => $minatBidangs['IOT']->id,
                    'area_riset_id' => $areaRisets['IOT']?->id,
                    'skor_boost' => 20,
                ],
                $minatBidangs['IOT'],
                $areaRisets['IOT'],
                10
            );
        }

        // Rule 11: Jika semua asesmen rendah (<= 2), berikan rekomendasi dasar
        $createRule(
            'RULE_DEFAULT_LOW',
            'Asesmen Rendah - Rekomendasi Dasar',
            'Jika semua asesmen <= 2, berikan rekomendasi area riset dasar',
            [
                'conditions' => [
                    ['pertanyaan' => 'ASESMEN_RPL_01', 'nilai' => 2, 'operator' => '<='],
                ]
            ],
            [
                'skor_boost' => 5,
            ],
            null,
            null,
            50
        );
    }
}
