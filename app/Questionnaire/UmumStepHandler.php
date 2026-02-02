<?php

namespace App\Questionnaire;

use App\Models\Konsultasi;
use App\Models\OpsiJawaban;
use App\Models\Pertanyaan;

class UmumStepHandler implements StepHandler
{
    public function handle(Konsultasi $konsultasi, OpsiJawaban $jawaban, array $currentState): array
    {
        $archetype = str_replace('ARKETIPE_', '', $jawaban->kode_jawaban);
        $nextKodePertanyaan = 'MINAT_' . $archetype . '_01';
        $nextQuestion = Pertanyaan::where('kode_pertanyaan', $nextKodePertanyaan)->with('opsiJawaban', 'kategori')->first();

        return [
            'pertanyaanSekarang' => $nextQuestion,
            'archetype' => $archetype,
            'progress' => 33,
        ];
    }
}
