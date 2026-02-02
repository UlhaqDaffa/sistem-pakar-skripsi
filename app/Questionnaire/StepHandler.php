<?php

namespace App\Questionnaire;

use App\Models\Konsultasi;
use App\Models\OpsiJawaban;

interface StepHandler
{
    /**
     * Handle the user's answer and determine the next step.
     *
     * @param Konsultasi $konsultasi The current consultation session.
     * @param OpsiJawaban $jawaban The answer chosen by the user.
     * @param array $currentState The current state of the questionnaire from the Livewire component.
     * @return array The new state, including the next question and other data.
     */
    public function handle(Konsultasi $konsultasi, OpsiJawaban $jawaban, array $currentState): array;
}
