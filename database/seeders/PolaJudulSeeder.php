<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PolaJudul;
use App\Models\AreaRiset;
use Illuminate\Support\Facades\DB;

class PolaJudulSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('area_riset_pola_judul')->truncate();
        PolaJudul::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Pola 1: Perbandingan Metode
        $pola1 = PolaJudul::create([
            'template_string' => 'Analisis Perbandingan Kinerja [METODE_A] dan [METODE_B] dalam [TUJUAN_MASALAH]',
            'deskripsi' => 'Pola untuk membandingkan dua metode dalam menyelesaikan suatu masalah',
        ]);

        // Pola 2: Penerapan Metode
        $pola2 = PolaJudul::create([
            'template_string' => 'Penerapan Metode [METODE_KUNCI] untuk [TUJUAN_MASALAH] (Studi Kasus: [TIPE_SISTEM])',
            'deskripsi' => 'Pola untuk menerapkan satu metode pada studi kasus tertentu',
        ]);

        // Pola 3: Pengembangan Sistem
        $pola3 = PolaJudul::create([
            'template_string' => 'Pengembangan Sistem [NAMA_AREA] Menggunakan [METODE_KUNCI] Berbasis [TIPE_SISTEM]',
            'deskripsi' => 'Pola untuk pengembangan sistem dengan metode tertentu',
        ]);

        // Pola 4: Optimasi dengan Metode
        $pola4 = PolaJudul::create([
            'template_string' => 'Optimasi [TUJUAN_MASALAH] Menggunakan [METODE_KUNCI] pada [TIPE_SISTEM]',
            'deskripsi' => 'Pola untuk optimasi dengan metode tertentu',
        ]);

        // Pola 5: Analisis dan Implementasi
        $pola5 = PolaJudul::create([
            'template_string' => 'Analisis dan Implementasi [NAMA_AREA] dengan Metode [METODE_A] untuk [TUJUAN_MASALAH]',
            'deskripsi' => 'Pola untuk analisis dan implementasi sistem',
        ]);

        // Pola 6: Sistem Hybrid
        $pola6 = PolaJudul::create([
            'template_string' => 'Pengembangan Sistem [NAMA_AREA] Hybrid Menggunakan [METODE_A] dan [METODE_B]',
            'deskripsi' => 'Pola untuk sistem hybrid yang menggabungkan dua metode',
        ]);

        // Pola 7: Evaluasi Metode
        $pola7 = PolaJudul::create([
            'template_string' => 'Evaluasi Kinerja Metode [METODE_KUNCI] dalam [NAMA_AREA] untuk [TUJUAN_MASALAH]',
            'deskripsi' => 'Pola untuk evaluasi kinerja suatu metode',
        ]);

        // Link pola judul ke area riset yang relevan
        $areaRisets = AreaRiset::with('tags')->get();

        foreach ($areaRisets as $areaRiset) {
            // Setiap area riset mendapat beberapa pola judul
            // Pola 1 (Perbandingan) - untuk area yang punya minimal 2 metode
            if ($areaRiset->tags->where('tipe', 'METODE')->count() >= 2) {
                $areaRiset->polaJuduls()->syncWithoutDetaching($pola1->id);
            }

            // Pola 2 (Penerapan) - untuk semua area
            $areaRiset->polaJuduls()->syncWithoutDetaching($pola2->id);

            // Pola 3 (Pengembangan) - untuk area pengembangan
            if (in_array($areaRiset->kode_area, ['PENG-01', 'PENG-02', 'PENG-03', 'RPL-01', 'RPL-02', 'RPL-03'])) {
                $areaRiset->polaJuduls()->syncWithoutDetaching($pola3->id);
            }

            // Pola 4 (Optimasi) - untuk area AI dan optimasi
            if (in_array($areaRiset->kode_area, ['AI-04', 'JAR-03', 'IOT-03'])) {
                $areaRiset->polaJuduls()->syncWithoutDetaching($pola4->id);
            }

            // Pola 5 (Analisis) - untuk area analisis
            if (in_array($areaRiset->kode_area, ['DATA-01', 'DATA-02', 'DATA-03', 'CITRA-01', 'CITRA-02', 'CITRA-03'])) {
                $areaRiset->polaJuduls()->syncWithoutDetaching($pola5->id);
            }

            // Pola 6 (Hybrid) - untuk area yang bisa dikombinasikan
            if (in_array($areaRiset->kode_area, ['DATA-02', 'AI-01', 'AI-02'])) {
                $areaRiset->polaJuduls()->syncWithoutDetaching($pola6->id);
            }

            // Pola 7 (Evaluasi) - untuk semua area
            $areaRiset->polaJuduls()->syncWithoutDetaching($pola7->id);
        }
    }
}
