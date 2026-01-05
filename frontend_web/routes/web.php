<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CvAiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini Anda mendaftarkan route web untuk aplikasi Anda.
|
*/

// 1. Halaman Utama (Form Upload CV)
Route::get('/', [CvAiController::class, 'index'])->name('home');

// 2. Proses Analisis CV (POST dari Form Upload)
Route::post('/analyze', [CvAiController::class, 'analyze'])->name('cv.analyze');

// 3. Memulai Sesi Wawancara (Generate Pertanyaan Pembuka)
Route::get('/interview/start', [CvAiController::class, 'startInterview'])->name('interview.start');

// 4. Proses Chat Wawancara (AJAX Request untuk Chat)
Route::post('/interview/chat', [CvAiController::class, 'chatProcess'])->name('interview.chat');