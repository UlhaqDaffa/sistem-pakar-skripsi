<?php

namespace App\Questionnaire;

use App\Models\Konsultasi;
use App\Models\OpsiJawaban;

class NilaiStepHandler implements StepHandler
{
    public function handle(Konsultasi $konsultasi, OpsiJawaban $jawaban, array $currentState): array
    {
        // Set flag untuk menampilkan form input nilai
        return [
            'showNilaiForm' => true,
            'pertanyaanSekarang' => null,
            'progress' => 90,
        ];
    }
}

