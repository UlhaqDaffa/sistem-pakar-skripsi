<?php

namespace App\Http\Controllers;

use App\Models\Konsultasi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class EksporController extends Controller
{
    /**
     * Export single consultation to PDF
     */
    public function exportPdf($konsultasi)
    {
        $consultation = Konsultasi::where('user_id', Auth::id())
            ->where('status', 'selesai')
            ->with([
                'areaRisetFinal.tags',
                'hasilMinat',
                'hasilAkademik',
                'jawabanKonsultasis.opsiJawaban.pertanyaan.kategori',
                'nilaiMataKuliah.mataKuliahKunci',
                'user'
            ])
            ->findOrFail($konsultasi);

        $pdf = Pdf::loadView('pdf.konsultasi', [
            'consultation' => $consultation,
            'user' => Auth::user(),
        ])->setPaper('a4', 'portrait');

        $filename = 'konsultasi-' . $consultation->id . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export all consultations to PDF
     */
    public function exportAllPdf()
    {
        $consultations = Konsultasi::where('user_id', Auth::id())
            ->where('status', 'selesai')
            ->with([
                'areaRisetFinal.tags',
                'hasilMinat',
                'hasilAkademik',
                'jawabanKonsultasis.opsiJawaban.pertanyaan.kategori',
                'nilaiMataKuliah.mataKuliahKunci',
            ])
            ->latest()
            ->get();

        if ($consultations->isEmpty()) {
            return redirect()->route('ekspor.index')
                ->with('error', 'Tidak ada konsultasi yang dapat diekspor.');
        }

        $pdf = Pdf::loadView('pdf.semua-konsultasi', [
            'consultations' => $consultations,
            'user' => Auth::user(),
        ])->setPaper('a4', 'portrait');

        $filename = 'semua-konsultasi-' . Auth::user()->name . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}
