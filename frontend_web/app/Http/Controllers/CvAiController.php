<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Smalot\PdfParser\Parser;

class CvAiController extends Controller
{
    // URL Backend Python
    protected $aiUrl = 'https://backend-ai-rosy.vercel.app';

    public function index()
    {
        return view('cv.index');
    }

    public function analyze(Request $request)
    {
        // 1. VALIDASI FORMAT (Laravel Validation)
        $request->validate([
            'cv_file' => 'required|mimes:pdf|max:2048', // Max 2MB
            'job_desc' => 'required|string|min:50',      // Minimal 50 karakter
        ], [
            // Custom Error Messages (Bahasa Indonesia)
            'cv_file.required' => 'Anda wajib mengunggah file CV.',
            'cv_file.mimes' => 'Format file harus PDF. File Word/Gambar tidak didukung.',
            'cv_file.max' => 'Ukuran file terlalu besar. Maksimal 2MB.',
            'job_desc.required' => 'Deskripsi pekerjaan tidak boleh kosong.',
            'job_desc.min' => 'Deskripsi pekerjaan terlalu pendek. Mohon copy-paste info loker yang lengkap.',
        ]);

        try {
            // 2. EKSTRAKSI TEKS
            $parser = new Parser();
            try {
                $pdf = $parser->parseFile($request->file('cv_file')->getPathname());
                $cvText = $pdf->getText();
            } catch (\Exception $e) {
                return back()->withErrors(['cv_file' => 'File PDF rusak atau tidak bisa dibaca sistem.'])->withInput();
            }

            // 3. VALIDASI ISI KONTEN (Anti "Sembarang File")
            
            // Cek 1: Apakah teksnya kosong? (Misal hasil scan gambar)
            if (strlen(trim($cvText)) < 100) {
                return back()->withErrors(['cv_file' => 'Isi PDF tidak terbaca atau terlalu sedikit. Pastikan PDF berisi teks, bukan hasil scan gambar.'])->withInput();
            }

            // Cek 2: Deteksi Keyword CV (Apakah ini beneran CV?)
            $cvKeywords = ['pengalaman', 'pendidikan', 'experience', 'education', 'skill', 'keahlian', 'profil', 'profile', 'riwayat', 'kerja', 'work'];
            $isLikelyCV = false;
            
            foreach ($cvKeywords as $keyword) {
                if (stripos($cvText, $keyword) !== false) {
                    $isLikelyCV = true;
                    break;
                }
            }

            if (!$isLikelyCV) {
                return back()->withErrors(['cv_file' => 'File yang diupload sepertinya bukan CV. Pastikan dokumen memuat bagian "Pendidikan" atau "Pengalaman".'])->withInput();
            }

            // 4. KIRIM KE AI
            $response = Http::timeout(120)->post($this->aiUrl . '/analyze-cv', [
                'cv_text' => $cvText,
                'job_desc' => $request->job_desc,
            ]);

            if ($response->failed()) {
                return back()->with('error', 'Gagal terhubung ke AI Engine. Coba lagi nanti.');
            }

            $result = $response->json();

            // Simpan Session
            Session::put('cv_context', $cvText);
            Session::put('job_context', $request->job_desc);
            Session::put('analysis_result', $result);

            return view('cv.result', ['data' => $result]);

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    // Method lainnya tetap sama...
    public function showResult()
    {
        if (!Session::has('analysis_result')) return redirect()->route('home');
        return view('cv.result', ['data' => Session::get('analysis_result')]);
    }

    public function generateCoverLetter()
    {
        if (!Session::has('cv_context')) return redirect()->route('home');
        
        try {
            $response = Http::timeout(60)->post($this->aiUrl . '/generate-cover-letter', [
                'cv_text' => Session::get('cv_context'),
                'job_desc' => Session::get('job_context'),
            ]);
            return view('cv.cover_letter', ['cover_letter' => $response->json()['cover_letter'] ?? 'Gagal membuat surat.']);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function startInterview()
    {
        if (!Session::has('cv_context')) return redirect()->route('home');

        try {
            $response = Http::timeout(60)->post($this->aiUrl . '/interview/start', [
                'cv_text' => Session::get('cv_context'),
                'job_desc' => Session::get('job_context'),
            ]);
            Session::put('chat_history', []);
            return view('cv.interview', ['firstQuestion' => $response->json()['message'] ?? 'Halo.']);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function chatProcess(Request $request)
    {
        $response = Http::timeout(60)->post($this->aiUrl . '/interview/chat', [
            'history' => Session::get('chat_history', []),
            'message' => $request->message,
            'context' => Session::get('cv_context') . "\n\n[JOB DESC]\n" . Session::get('job_context')
        ]);

        if ($response->failed()) return response()->json(['error' => 'Error'], 500);

        $history = Session::get('chat_history', []);
        $history[] = ['role' => 'user', 'content' => $request->message];
        $history[] = ['role' => 'assistant', 'content' => $response->json()['reply']];
        Session::put('chat_history', $history);

        return response()->json(['reply' => $response->json()['reply']]);
    }
}