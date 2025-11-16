<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hanya jalankan jika ada data existing
        if (DB::table('opsi_jawaban')->count() === 0) {
            return;
        }

        // 1. Kelompokkan opsi jawaban per pertanyaan dan buat signature
        $pertanyaanOpsiMap = [];
        $pertanyaanData = DB::table('pertanyaan')->get();

        foreach ($pertanyaanData as $pertanyaan) {
            $opsiJawaban = DB::table('opsi_jawaban')
                ->where('pertanyaan_id', $pertanyaan->id)
                ->orderBy('nilai')
                ->get();

            if ($opsiJawaban->isEmpty()) {
                continue;
            }

            // Buat signature dari set opsi jawaban
            $signature = $opsiJawaban->map(function ($opsi) {
                return $opsi->kode_jawaban . '|' . $opsi->teks_jawaban . '|' . $opsi->nilai;
            })->implode('||');

            $pertanyaanOpsiMap[$pertanyaan->id] = [
                'pertanyaan' => $pertanyaan,
                'opsi' => $opsiJawaban,
                'signature' => $signature,
            ];
        }

        // 2. Kelompokkan pertanyaan berdasarkan signature (set opsi yang sama)
        $templateGroups = [];
        foreach ($pertanyaanOpsiMap as $pertanyaanId => $data) {
            $signature = $data['signature'];
            if (!isset($templateGroups[$signature])) {
                $templateGroups[$signature] = [];
            }
            $templateGroups[$signature][] = $pertanyaanId;
        }

        // 3. Buat template untuk setiap grup unik
        $templateMap = []; // Map dari signature ke template_id
        $templateCounter = 1;

        foreach ($templateGroups as $signature => $pertanyaanIds) {
            // Ambil data opsi dari pertanyaan pertama dalam grup
            $firstPertanyaanId = $pertanyaanIds[0];
            $opsiData = $pertanyaanOpsiMap[$firstPertanyaanId]['opsi'];
            $pertanyaan = $pertanyaanOpsiMap[$firstPertanyaanId]['pertanyaan'];

            // Tentukan nama template berdasarkan kategori atau kode pertanyaan
            $kategori = DB::table('kategori_pertanyaan')->where('id', $pertanyaan->kategori_id)->first();
            $templateName = $this->generateTemplateName($kategori, $opsiData);
            $templateCode = 'TEMPLATE_' . strtoupper(str_replace(' ', '_', $templateName)) . '_' . $templateCounter;

            // Buat template
            $templateId = DB::table('opsi_jawaban_template')->insertGetId([
                'kode_template' => $templateCode,
                'nama_template' => $templateName,
                'deskripsi' => "Template untuk pertanyaan kategori {$kategori->nama_kategori}",
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Buat item-item template
            $urutan = 0;
            foreach ($opsiData as $opsi) {
                DB::table('opsi_jawaban_template_item')->insert([
                    'template_id' => $templateId,
                    'kode_jawaban' => $opsi->kode_jawaban,
                    'teks_jawaban' => $opsi->teks_jawaban,
                    'nilai' => $opsi->nilai,
                    'urutan' => $urutan++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Link semua pertanyaan dalam grup ke template ini
            foreach ($pertanyaanIds as $pertanyaanId) {
                DB::table('pertanyaan_opsi_jawaban_template')->insert([
                    'pertanyaan_id' => $pertanyaanId,
                    'template_id' => $templateId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $templateMap[$signature] = $templateId;
            $templateCounter++;
        }
    }

    /**
     * Generate template name based on category and options
     */
    private function generateTemplateName($kategori, $opsiData): string
    {
        if ($kategori->kode_kategori === 'ASESMEN') {
            // Semua asesmen menggunakan skala Likert
            return 'Skala Likert 1-5';
        } elseif ($kategori->kode_kategori === 'UMUM') {
            return 'Opsi Arketipe';
        } elseif ($kategori->kode_kategori === 'MINAT') {
            return 'Opsi Minat Bidang';
        }

        // Default: berdasarkan jumlah opsi
        return 'Opsi ' . $opsiData->count() . ' Pilihan';
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus semua data template
        DB::table('pertanyaan_opsi_jawaban_template')->delete();
        DB::table('opsi_jawaban_template_item')->delete();
        DB::table('opsi_jawaban_template')->delete();
    }
};
