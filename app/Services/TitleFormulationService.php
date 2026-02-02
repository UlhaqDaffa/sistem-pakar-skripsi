<?php

namespace App\Services;

use App\Models\AreaRiset;
use Illuminate\Support\Collection;

class TitleFormulationService
{
    /**
     * Generasi judul berdasarkan pola judul yang terhubung dengan area riset
     *
     * @param AreaRiset $areaRiset
     * @return array Array of formatted title strings
     */
    public function generateTitles(AreaRiset $areaRiset): array
    {
        $areaRiset->loadMissing('tags');
        // Ambil semua pola judul yang terhubung dengan area riset
        $polaJuduls = $areaRiset->polaJuduls;

        if ($polaJuduls->isEmpty()) {
            return [];
        }

        $metodeArray = $this->extractMetodeTags($areaRiset);

        $titles = [];

        foreach ($polaJuduls as $polaJudul) {
            $template = $polaJudul->template_string;

            // Lakukan string replacement
            $formattedTitle = $this->replacePlaceholders($template, $areaRiset, $metodeArray);

            $titles[] = $formattedTitle;
        }

        return $titles;
    }

    private function extractMetodeTags(AreaRiset $areaRiset): array
    {
        return $areaRiset->tags
            ->where('tipe', 'METODE')
            ->pluck('nama_tag')
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Replace placeholders in template string
     *
     * @param string $template
     * @param AreaRiset $areaRiset
     * @param array $metodeArray
     * @return string
     */
    private function replacePlaceholders(string $template, AreaRiset $areaRiset, array $metodeArray): string
    {
        $result = $template;

        // [METODE_A] → metode pertama
        $metodeA = $metodeArray[0] ?? '';
        $result = str_replace('[METODE_A]', $metodeA, $result);

        // [METODE_B] → metode kedua
        $metodeB = $metodeArray[1] ?? '';
        $result = str_replace('[METODE_B]', $metodeB, $result);

        // [METODE_KUNCI] → metode pertama (sama dengan METODE_A)
        $result = str_replace('[METODE_KUNCI]', $metodeA, $result);

        // [NAMA_AREA] → nama_area dari AreaRiset
        $result = str_replace('[NAMA_AREA]', $areaRiset->nama_area, $result);

        // [TUJUAN_MASALAH] → tujuan_masalah dari AreaRiset
        $result = str_replace('[TUJUAN_MASALAH]', $areaRiset->tujuan_masalah ?? '', $result);

        // [TIPE_SISTEM] → tipe_sistem dari AreaRiset
        $result = str_replace('[TIPE_SISTEM]', $areaRiset->tipe_sistem ?? '', $result);

        return $result;
    }
}
