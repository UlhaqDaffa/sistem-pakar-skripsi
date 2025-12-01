<?php

namespace App\Services;

use App\Models\AreaRiset;
use App\Models\NilaiMataKuliah;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class DecisionTreeService
{
    private string $apiUrl;
    private int $timeout;
    private int $retryAttempts;

    public function __construct()
    {
        $this->apiUrl = config('decision_tree.api_url');
        $this->timeout = config('decision_tree.timeout', 30);
        $this->retryAttempts = config('decision_tree.retry_attempts', 3);
    }

    /**
     * Prediksi area riset berdasarkan nilai mata kuliah
     * 
     * @param array $nilaiMataKuliah Array dengan key: 'algoritma', 'pemrograman', 'basis_data', 'kecerdasan_buatan'
     * @return AreaRiset|null
     */
    public function predictAreaRiset(array $nilaiMataKuliah): ?AreaRiset
    {
        try {
            // Validasi input
            $requiredKeys = ['algoritma', 'pemrograman', 'basis_data', 'kecerdasan_buatan'];
            foreach ($requiredKeys as $key) {
                if (!isset($nilaiMataKuliah[$key])) {
                    throw new Exception("Key '{$key}' tidak ditemukan dalam input");
                }
                if (!is_numeric($nilaiMataKuliah[$key]) || $nilaiMataKuliah[$key] < 1 || $nilaiMataKuliah[$key] > 5) {
                    throw new Exception("Nilai '{$key}' harus antara 1-5");
                }
            }

            // Format data untuk API
            $requestData = [
                'algoritma' => (float) $nilaiMataKuliah['algoritma'],
                'pemrograman' => (float) $nilaiMataKuliah['pemrograman'],
                'basis_data' => (float) $nilaiMataKuliah['basis_data'],
                'kecerdasan_buatan' => (float) $nilaiMataKuliah['kecerdasan_buatan'],
            ];

            // Panggil API dengan retry mechanism
            $response = $this->callApiWithRetry('/api/predict', $requestData);

            if (!$response) {
                Log::error('Decision Tree API: Tidak ada response');
                return null;
            }

            // Parse response
            $kodeArea = $response['kode_area'] ?? null;
            $confidence = $response['confidence'] ?? 0;

            if (!$kodeArea) {
                Log::warning('Decision Tree API: kode_area tidak ditemukan dalam response', ['response' => $response]);
                return null;
            }

            // Cari AreaRiset berdasarkan kode_area
            $areaRiset = AreaRiset::where('kode_area', $kodeArea)->first();

            if (!$areaRiset) {
                Log::warning('Decision Tree API: AreaRiset tidak ditemukan untuk kode_area', ['kode_area' => $kodeArea]);
                return null;
            }

            Log::info('Decision Tree API: Prediksi berhasil', [
                'kode_area' => $kodeArea,
                'confidence' => $confidence,
                'area_riset_id' => $areaRiset->id
            ]);

            return $areaRiset;

        } catch (Exception $e) {
            Log::error('Decision Tree API: Error saat prediksi', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Prediksi dari koleksi NilaiMataKuliah
     * 
     * @param \Illuminate\Support\Collection $nilaiMataKuliahCollection
     * @return AreaRiset|null
     */
    public function predictFromCollection($nilaiMataKuliahCollection): ?AreaRiset
    {
        // Convert collection ke array format
        $nilaiArray = [];
        
        foreach ($nilaiMataKuliahCollection as $nilai) {
            $kodeMataKuliah = $nilai->mataKuliahKunci->kode_mata_kuliah ?? null;
            if (!$kodeMataKuliah) {
                continue;
            }

            // Map kode_mata_kuliah ke key yang diharapkan API
            $key = $this->mapKodeMataKuliahToKey($kodeMataKuliah);
            if ($key) {
                $nilaiArray[$key] = (float) $nilai->nilai;
            }
        }

        // Pastikan semua key ada
        $requiredKeys = ['algoritma', 'pemrograman', 'basis_data', 'kecerdasan_buatan'];
        foreach ($requiredKeys as $key) {
            if (!isset($nilaiArray[$key])) {
                Log::warning("Decision Tree Service: Key '{$key}' tidak ditemukan dalam nilai mata kuliah");
                return null;
            }
        }

        return $this->predictAreaRiset($nilaiArray);
    }

    /**
     * Map kode_mata_kuliah ke key yang diharapkan API
     */
    private function mapKodeMataKuliahToKey(string $kodeMataKuliah): ?string
    {
        $mapping = [
            'ALGORITMA' => 'algoritma',
            'PEMROGRAMAN' => 'pemrograman',
            'BASIS_DATA' => 'basis_data',
            'KECERDASAN_BUATAN' => 'kecerdasan_buatan',
        ];

        return $mapping[strtoupper($kodeMataKuliah)] ?? null;
    }

    /**
     * Panggil API dengan retry mechanism
     */
    private function callApiWithRetry(string $endpoint, array $data): ?array
    {
        $url = rtrim($this->apiUrl, '/') . $endpoint;
        $lastException = null;

        for ($attempt = 1; $attempt <= $this->retryAttempts; $attempt++) {
            try {
                $response = Http::timeout($this->timeout)
                    ->post($url, $data);

                if ($response->successful()) {
                    return $response->json();
                }

                // Jika response tidak successful, log dan retry
                Log::warning("Decision Tree API: Request gagal (attempt {$attempt})", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                if ($attempt < $this->retryAttempts) {
                    sleep(1); // Wait 1 second before retry
                }

            } catch (Exception $e) {
                $lastException = $e;
                Log::warning("Decision Tree API: Exception (attempt {$attempt})", [
                    'message' => $e->getMessage()
                ]);

                if ($attempt < $this->retryAttempts) {
                    sleep(1);
                }
            }
        }

        if ($lastException) {
            throw $lastException;
        }

        return null;
    }

    /**
     * Health check API
     */
    public function healthCheck(): bool
    {
        try {
            $url = rtrim($this->apiUrl, '/') . '/api/health';
            $response = Http::timeout(5)->get($url);

            return $response->successful() && ($response->json()['status'] ?? null) === 'healthy';
        } catch (Exception $e) {
            Log::error('Decision Tree API: Health check gagal', ['message' => $e->getMessage()]);
            return false;
        }
    }
}

