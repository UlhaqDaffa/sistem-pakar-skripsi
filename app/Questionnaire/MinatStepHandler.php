<?php

namespace App\Questionnaire;

use App\Models\Konsultasi;
use App\Models\OpsiJawaban;
use App\Models\Pertanyaan;

class MinatStepHandler implements StepHandler
{
    public function handle(Konsultasi $konsultasi, OpsiJawaban $jawaban, array $currentState): array
    {
        $minat = str_replace('MINAT_', '', $jawaban->kode_jawaban);
        $asesmenKode = 'ASESMEN_' . $minat . '_%';
        $antrianAsesmen = Pertanyaan::where('kode_pertanyaan', 'like', $asesmenKode)
            ->with('opsiJawaban', 'kategori')
            ->orderBy('id')
            ->get()->all();

        $nextQuestion = $antrianAsesmen[0] ?? null;

        return [
            'pertanyaanSekarang' => $nextQuestion,
            'minat' => $minat,
            'antrianAsesmen' => $antrianAsesmen,
            'asesmenIndex' => 0,
            'progress' => 66,
        ];
    }
}
