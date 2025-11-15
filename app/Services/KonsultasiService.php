<?php

namespace App\Services;

use App\Models\JawabanKonsultasi;
use App\Models\Konsultasi;
use App\Models\OpsiJawaban;
use App\Models\Pertanyaan;
use App\Questionnaire\AsesmenStepHandler;
use App\Questionnaire\MinatStepHandler;
use App\Questionnaire\StepHandler;
use App\Questionnaire\UmumStepHandler;
use Exception;

class KonsultasiService
{
    public function start(): Konsultasi
    {
        return Konsultasi::create(['user_id' => auth()->id()]);
    }

    public function getStartQuestion(): ?Pertanyaan
    {
        return Pertanyaan::where('is_start_point', true)->with('opsiJawaban', 'kategori')->first();
    }

    public function processAnswer(Konsultasi $konsultasi, Pertanyaan $currentQuestion, OpsiJawaban $jawaban, array $currentState): array
    {
        JawabanKonsultasi::create([
            'konsultasi_id' => $konsultasi->id,
            'opsi_jawaban_id' => $jawaban->id,
        ]);

        $handler = $this->getHandlerForQuestion($currentQuestion);
        return $handler->handle($konsultasi, $jawaban, $currentState);
    }

    public function finish(Konsultasi $konsultasi): void
    {
        $konsultasi->status = 'selesai';
        // Logika untuk menganalisis jawaban dan menyimpan hasil bisa ditambahkan di sini
        // $konsultasi->hasil_minat_id = ...
        $konsultasi->save();
    }

    private function getHandlerForQuestion(Pertanyaan $question): StepHandler
    {
        $kodeKategori = $question->kategori->kode_kategori;

        switch ($kodeKategori) {
            case 'UMUM':
                return new UmumStepHandler();
            case 'MINAT':
                return new MinatStepHandler();
            case 'ASESMEN':
                return new AsesmenStepHandler();
            default:
                throw new Exception("Handler tidak ditemukan untuk kategori: " . $kodeKategori);
        }
    }
}
