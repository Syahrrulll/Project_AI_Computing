<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Career Assistant - Upload</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800">

    <!-- Loading Overlay (Hidden by Default) -->
    <div id="loading-overlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center transition-opacity duration-300">
        <div class="bg-white p-8 rounded-3xl shadow-2xl flex flex-col items-center max-w-sm text-center transform scale-100 transition-transform duration-300">
            <div class="relative mb-6">
                <!-- Spinner Ring -->
                <div class="w-16 h-16 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin"></div>
                <!-- Icon Tengah -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-xs font-bold text-indigo-600">AI</span>
                </div>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">Menganalisis Dokumen...</h3>
            <p class="text-sm text-slate-500">Mohon tunggu sebentar, AI sedang membaca CV Anda dan mencocokkannya dengan lowongan.</p>
        </div>
    </div>

    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-4xl w-full">
            
            <div class="text-center mb-10">
                <h1 class="text-4xl font-bold text-indigo-700 mb-2">CV Optimizer & Interview Simulator</h1>
                <p class="text-slate-500">Analisis kekuatan CV Anda dan latihan wawancara dengan AI HRD.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-200">
                <div class="p-8 md:p-10">
                    
                    <!-- Alert Global (Untuk Error Sistem/Server) -->
                    @if(session('error'))
                        <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 flex items-center border border-red-100 animate-pulse">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <!-- Alert Error Validasi Umum (Jika ada banyak error) -->
                    @if ($errors->any())
                        <div class="bg-amber-50 text-amber-700 p-4 rounded-lg mb-6 border border-amber-100">
                            <p class="font-bold">Mohon perbaiki kesalahan berikut:</p>
                            <ul class="list-disc list-inside text-sm mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="upload-form" action="{{ route('cv.analyze') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid md:grid-cols-2 gap-8">
                            <!-- Kolom Kiri: Upload -->
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">1. Upload CV (PDF)</label>
                                    <div class="relative group">
                                        <!-- Update: Tambahkan event onchange ke fungsi JS -->
                                        <input type="file" name="cv_file" id="cv_file" class="hidden" accept="application/pdf" onchange="handleFileSelect(this)">
                                        
                                        <!-- Container Upload dengan Error State -->
                                        <label for="cv_file" class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed rounded-xl cursor-pointer transition-colors
                                            @error('cv_file') border-red-300 bg-red-50 @else border-indigo-200 bg-indigo-50 hover:bg-indigo-100 @enderror">
                                            
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <svg class="w-10 h-10 mb-3 @error('cv_file') text-red-500 @else text-indigo-500 @enderror" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                                <p class="mb-2 text-sm @error('cv_file') text-red-600 @else text-indigo-600 @enderror font-semibold">Klik untuk upload CV</p>
                                                <p id="file-label" class="text-xs text-slate-500">PDF (Maks. 2MB)</p>
                                            </div>
                                        </label>
                                    </div>
                                    <!-- Pesan Error Spesifik Input File -->
                                    @error('cv_file')
                                        <p class="text-red-500 text-sm mt-2 font-medium flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Kolom Kanan: Job Desc -->
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">2. Tempel Deskripsi Lowongan</label>
                                    <textarea name="job_desc" rows="8" 
                                        class="w-full p-4 text-sm bg-slate-50 border rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition resize-none
                                        @error('job_desc') border-red-300 focus:ring-red-500 @else border-slate-200 @enderror" 
                                        placeholder="Contoh: Dicari Laravel Developer dengan pengalaman 2 tahun...">{{ old('job_desc') }}</textarea>
                                    
                                    <!-- Pesan Error Spesifik Textarea -->
                                    @error('job_desc')
                                        <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-slate-100">
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-indigo-200 transition transform hover:-translate-y-0.5 flex justify-center items-center gap-2">
                                <span>🚀 Mulai Analisis AI</span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Handling -->
    <script>
        // 1. Update Nama File saat dipilih agar user tau file sudah masuk
        function handleFileSelect(input) {
            if (input.files && input.files[0]) {
                const label = document.getElementById('file-label');
                label.innerText = input.files[0].name;
                label.classList.remove('text-slate-500');
                label.classList.add('text-indigo-600', 'font-bold');
            }
        }

        // 2. Tampilkan Loading saat Submit Form
        const form = document.getElementById('upload-form');
        const loadingOverlay = document.getElementById('loading-overlay');

        form.addEventListener('submit', function(e) {
            // Cek sederhana: Jangan munculkan loading jika file kosong (biar validasi browser jalan dulu)
            const fileInput = document.getElementById('cv_file');
            if (!fileInput.files.length) return; 

            // Munculkan Overlay
            loadingOverlay.classList.remove('hidden');
        });
    </script>

</body>
</html>