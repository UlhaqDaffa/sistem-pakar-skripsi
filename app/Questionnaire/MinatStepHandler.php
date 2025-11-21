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

        $discriminator = Pertanyaan::where('kode_pertanyaan', 'DISK_' . $minat . '_01')
            ->with(['opsiJawabanTemplate.opsiJawabanTemplateItems', 'opsiJawaban', 'kategori'])
            ->first();

        $asesmenPertanyaan = Pertanyaan::where('kode_pertanyaan', 'like', $asesmenKode)
            ->with(['opsiJawabanTemplate.opsiJawabanTemplateItems', 'opsiJawaban', 'kategori'])
            ->orderBy('kode_pertanyaan')
            ->get()
            ->all();

        $antrianAsesmen = array_values(array_filter(array_merge(
            $discriminator ? [$discriminator] : [],
            $asesmenPertanyaan
        )));

        $nextQuestion = $antrianAsesmen[0] ?? null;

        return [
            'pertanyaanSekarang' => $nextQuestion,
            'minat' => $minat,
            'antrianAsesmen' => $antrianAsesmen,
            'asesmenIndex' => 0,
            'progress' => 60,
        ];
    }
}
