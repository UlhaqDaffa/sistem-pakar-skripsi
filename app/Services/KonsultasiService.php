<?php

namespace App\Services;

use App\Models\JawabanKonsultasi;
use App\Models\Konsultasi;
use App\Models\MataKuliahKunci;
use App\Models\NilaiMataKuliah;
use App\Models\OpsiJawaban;
use App\Models\OpsiJawabanTemplateItem;
use App\Models\Pertanyaan;
use App\Questionnaire\AsesmenStepHandler;
use App\Questionnaire\MinatStepHandler;
use App\Questionnaire\StepHandler;
use App\Questionnaire\UmumStepHandler;
use Exception;
use Illuminate\Support\Facades\DB;

class KonsultasiService
{
    public function start(): Konsultasi
    {
        return Konsultasi::create(['user_id' => auth()->id()]);
    }

    public function getStartQuestion(): ?Pertanyaan
    {
        return Pertanyaan::where('is_start_point', true)
            ->with(['opsiJawabanTemplate.opsiJawabanTemplateItems', 'opsiJawaban', 'kategori'])
            ->first();
    }

    public function processAnswer(Konsultasi $konsultasi, Pertanyaan $currentQuestion, $jawaban, array $currentState): array
    {
        // Handle both OpsiJawaban (legacy) and OpsiJawabanTemplateItem (new)
        $opsiJawabanId = null;
        
        if ($jawaban instanceof OpsiJawaban) {
            // Legacy: langsung gunakan ID
            $opsiJawabanId = $jawaban->id;
        } elseif ($jawaban instanceof OpsiJawabanTemplateItem) {
            // New: cari atau buat OpsiJawaban dari template item
            $opsiJawabanId = $this->getOrCreateOpsiJawabanFromTemplateItem($currentQuestion, $jawaban);
        } elseif (is_numeric($jawaban)) {
            // Jika yang dikirim adalah ID, coba cari sebagai template item dulu, lalu fallback ke OpsiJawaban
            $templateItem = OpsiJawabanTemplateItem::find($jawaban);
            if ($templateItem) {
                $opsiJawabanId = $this->getOrCreateOpsiJawabanFromTemplateItem($currentQuestion, $templateItem);
            } else {
                $opsiJawaban = OpsiJawaban::find($jawaban);
                if ($opsiJawaban) {
                    $opsiJawabanId = $opsiJawaban->id;
                }
            }
        }

        if (!$opsiJawabanId) {
            throw new Exception("Tidak dapat menemukan atau membuat opsi jawaban");
        }

        // Ambil OpsiJawaban untuk handler
        $opsiJawaban = OpsiJawaban::find($opsiJawabanId);

        $jawabanKonsultasi = JawabanKonsultasi::create([
            'konsultasi_id' => $konsultasi->id,
            'opsi_jawaban_id' => $opsiJawabanId,
        ]);

        $handler = $this->getHandlerForQuestion($currentQuestion);
        $result = $handler->handle($konsultasi, $opsiJawaban, $currentState);
        $result['jawaban_konsultasi_id'] = $jawabanKonsultasi->id;
        $result['pertanyaan_terjawab_id'] = $currentQuestion->id;

        return $result;
    }

    /**
     * Cari atau buat OpsiJawaban dari template item untuk backward compatibility
     */
    private function getOrCreateOpsiJawabanFromTemplateItem(Pertanyaan $pertanyaan, OpsiJawabanTemplateItem $templateItem): int
    {
        // Cari OpsiJawaban yang sudah ada dengan kode_jawaban yang sama untuk pertanyaan ini
        $existing = OpsiJawaban::where('pertanyaan_id', $pertanyaan->id)
            ->where('kode_jawaban', $templateItem->kode_jawaban)
            ->first();

        if ($existing) {
            return $existing->id;
        }

        // Buat OpsiJawaban baru dari template item (untuk backward compatibility dengan jawaban_konsultasi)
        $opsiJawaban = OpsiJawaban::create([
            'pertanyaan_id' => $pertanyaan->id,
            'kode_jawaban' => $templateItem->kode_jawaban,
            'teks_jawaban' => $templateItem->teks_jawaban,
            'nilai' => $templateItem->nilai,
        ]);

        return $opsiJawaban->id;
    }

    /**
     * Simpan nilai mata kuliah untuk konsultasi
     */
    public function saveNilaiMataKuliah(Konsultasi $konsultasi, array $nilaiData): void
    {
        // Mapping kode mata kuliah
        $mataKuliahMapping = [
            'algoritma' => 'ALGORITMA',
            'pemrograman' => 'PEMROGRAMAN',
            'basis_data' => 'BASIS_DATA',
            'kecerdasan_buatan' => 'KECERDASAN_BUATAN',
        ];

        DB::transaction(function () use ($konsultasi, $nilaiData, $mataKuliahMapping) {
            foreach ($nilaiData as $key => $nilai) {
                $kodeMataKuliah = $mataKuliahMapping[$key] ?? null;
                if (!$kodeMataKuliah) {
                    continue;
                }

                $mataKuliahKunci = MataKuliahKunci::where('kode_mata_kuliah', $kodeMataKuliah)->first();
                if (!$mataKuliahKunci) {
                    throw new Exception("Mata kuliah dengan kode '{$kodeMataKuliah}' tidak ditemukan");
                }

                // Konversi nilai ke skala numerik Decision Tree
                $nilaiAngka = $this->convertNilaiAEToAngka($nilai);

                // Hapus nilai lama jika ada
                NilaiMataKuliah::where('konsultasi_id', $konsultasi->id)
                    ->where('mata_kuliah_kunci_id', $mataKuliahKunci->id)
                    ->delete();

                // Simpan nilai baru (dalam format angka untuk Decision Tree)
                NilaiMataKuliah::create([
                    'konsultasi_id' => $konsultasi->id,
                    'mata_kuliah_kunci_id' => $mataKuliahKunci->id,
                    'nilai' => $nilaiAngka,
                ]);
            }
        });
    }

    /**
     * Selesaikan konsultasi dengan menghitung hasil
     */
    public function finish(Konsultasi $konsultasi): void
    {
        DB::transaction(function () use ($konsultasi) {
            // 1. Evaluasi menggunakan Rule-Based System (prioritas utama)
            $ruleBasedService = app(\App\Services\RuleBasedService::class);
            $hasilRuleBased = $ruleBasedService->evaluate($konsultasi);
            
            // 2. Hitung Hasil_Minat menggunakan ScoringService (sebagai fallback atau validasi)
            $scoringService = app(\App\Services\ScoringService::class);
            $hasilMinat = $scoringService->calculateHasilMinat($konsultasi);
            
            // Prioritas: Gunakan hasil Rule-Based jika ada, jika tidak gunakan ScoringService
            $hasilMinatFinal = $hasilRuleBased ?? $hasilMinat;
            
            if ($hasilMinatFinal) {
                $konsultasi->hasil_minat_id = $hasilMinatFinal->id;
            }

            // 3. Jika ada nilai mata kuliah, panggil DecisionTreeService untuk Hasil_Akademik
            $nilaiMataKuliah = $konsultasi->nilaiMataKuliah()->with('mataKuliahKunci')->get();
            $hasilAkademik = null;

            if ($nilaiMataKuliah->isNotEmpty()) {
                $decisionTreeService = app(\App\Services\DecisionTreeService::class);
                $hasilAkademik = $decisionTreeService->predictFromCollection($nilaiMataKuliah);
                
                if ($hasilAkademik) {
                    $konsultasi->hasil_akademik_id = $hasilAkademik->id;
                }
            }

            // 4. Rekonsiliasi Hasil_Minat dan Hasil_Akademik
            if ($hasilMinatFinal) {
                $rekonsiliasiService = app(\App\Services\RekonsiliasiService::class);
                $areaRisetFinal = $rekonsiliasiService->rekonsiliasi($hasilMinatFinal, $hasilAkademik);
                
                if ($areaRisetFinal) {
                    $konsultasi->area_riset_final_id = $areaRisetFinal->id;
                }
            }

            // 5. Update status menjadi selesai
            $konsultasi->status = 'selesai';
            $konsultasi->save();
        });
    }

    /**
     * Konversi nilai A-E ke angka
     * A = 4, B = 3, C = 2, D = 1, E = 0
     */
    private function convertNilaiAEToAngka(string|int|float $nilai): float
    {
        if (is_numeric($nilai)) {
            return (float) $nilai;
        }

        $scoringService = app(ScoringService::class);

        return (float) $scoringService->convertNilaiHurufKeSkor((string) $nilai);
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
            case 'DISK':
                return new AsesmenStepHandler();
            default:
                throw new Exception("Handler tidak ditemukan untuk kategori: " . $kodeKategori);
        }
    }
}
