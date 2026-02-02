<?php

namespace App\Questionnaire;

use App\Models\Konsultasi;
use App\Models\OpsiJawaban;

class AsesmenStepHandler implements StepHandler
{
    public function handle(Konsultasi $konsultasi, OpsiJawaban $jawaban, array $currentState): array
    {
        $newIndex = $currentState['asesmenIndex'] + 1;
        $antrian = $currentState['antrianAsesmen'];
        $nextQuestion = $antrian[$newIndex] ?? null;

        $totalAsesmen = count($antrian);
        $progress = 66;
        if ($totalAsesmen > 0) {
            $progress = 66 + (int)(($newIndex / $totalAsesmen) * 34);
        }

        // Jika tidak ada pertanyaan berikutnya, tampilkan form input nilai
        if (!$nextQuestion) {
            return [
                'pertanyaanSekarang' => null,
                'asesmenIndex' => $newIndex,
                'progress' => 90,
                'showNilaiForm' => true,
            ];
        }

        return [
            'pertanyaanSekarang' => $nextQuestion,
            'asesmenIndex' => $newIndex,
            'progress' => $progress,
            'showNilaiForm' => false,
        ];
    }
}
