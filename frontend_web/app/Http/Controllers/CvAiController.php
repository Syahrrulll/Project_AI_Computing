<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Smalot\PdfParser\Parser;

class CvAiController extends Controller
{
    // URL Backend Python (Pastikan main.py/ai_backend.py sudah berjalan di port 8001)
    protected $aiUrl = 'http://127.0.0.1:8001';

    /**
     * Halaman Utama (Upload)
     */
    public function index()
    {
        return view('cv.index');
    }

    /**
     * Proses Analisis CV
     */
    public function analyze(Request $request)
    {
        $request->validate([
            'cv_file' => 'required|mimes:pdf|max:2048',
            'job_desc' => 'required|string|min:20',
        ]);

        try {
            // 1. Ekstraksi Teks dari PDF
            $parser = new Parser();
            $pdf = $parser->parseFile($request->file('cv_file')->getPathname());
            $cvText = $pdf->getText();

            // 2. Kirim ke API Python (Chutes/Gemini Backend)
            $response = Http::timeout(120)->post($this->aiUrl . '/analyze-cv', [
                'cv_text' => $cvText,
                'job_desc' => $request->job_desc,
            ]);

            if ($response->failed()) {
                return back()->with('error', 'Gagal terhubung ke AI Engine. Pastikan backend Python berjalan.');
            }

            $result = $response->json();

            // 3. Simpan data ke Session untuk konteks wawancara nanti
            Session::put('cv_context', $cvText);
            Session::put('job_context', $request->job_desc);
            Session::put('analysis_result', $result);

            return view('cv.result', ['data' => $result]);

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Memulai Sesi Wawancara
     */
    public function startInterview()
    {
        if (!Session::has('cv_context')) {
            return redirect()->route('home')->with('error', 'Silakan analisis CV terlebih dahulu.');
        }

        try {
            // Minta pertanyaan pembuka ke AI
            $response = Http::timeout(60)->post($this->aiUrl . '/interview/start', [
                'cv_text' => Session::get('cv_context'),
                'job_desc' => Session::get('job_context'),
            ]);
    
            $firstQuestion = $response->json()['message'] ?? 'Halo, silakan perkenalkan diri Anda.';
            
            // Reset Chat History di Session
            Session::put('chat_history', []);
    
            return view('cv.interview', ['firstQuestion' => $firstQuestion]);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memulai wawancara: ' . $e->getMessage());
        }
    }

    /**
     * Proses Chat Wawancara (AJAX)
     */
    public function chatProcess(Request $request)
    {
        // Ambil history saat ini
        $history = Session::get('chat_history', []);
        
        // Kirim ke AI dengan history lengkap
        $response = Http::timeout(60)->post($this->aiUrl . '/interview/chat', [
            'history' => $history, // Kirim history agar AI ingat konteks
            'message' => $request->message,
            'context' => Session::get('cv_context') . "\n\n[JOB DESC]\n" . Session::get('job_context')
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Gagal membalas pesan.'], 500);
        }

        $reply = $response->json()['reply'];

        // Update History: Tambahkan pesan User & AI
        // Format disesuaikan dengan standar OpenAI/Chutes (role: user/assistant)
        $history[] = ['role' => 'user', 'content' => $request->message];
        $history[] = ['role' => 'assistant', 'content' => $reply];
        
        Session::put('chat_history', $history);

        return response()->json(['reply' => $reply]);
    }
}