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

    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-4xl w-full">
            
            <div class="text-center mb-10">
                <h1 class="text-4xl font-bold text-indigo-700 mb-2">CV Optimizer & Interview Simulator</h1>
                <p class="text-slate-500">Analisis kekuatan CV Anda dan latihan wawancara dengan AI HRD.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-200">
                <div class="p-8 md:p-10">
                    
                    @if(session('error'))
                        <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 flex items-center border border-red-100">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('cv.analyze') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid md:grid-cols-2 gap-8">
                            <!-- Kolom Kiri: Upload -->
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">1. Upload CV (PDF)</label>
                                    <div class="relative group">
                                        <input type="file" name="cv_file" id="cv_file" class="hidden" required accept="application/pdf" onchange="document.getElementById('file-label').innerText = this.files[0].name">
                                        <label for="cv_file" class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-indigo-200 rounded-xl cursor-pointer bg-indigo-50 hover:bg-indigo-100 transition-colors">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <svg class="w-10 h-10 mb-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                                <p class="mb-2 text-sm text-indigo-600 font-semibold">Klik untuk upload CV</p>
                                                <p id="file-label" class="text-xs text-slate-500">PDF (Maks. 2MB)</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom Kanan: Job Desc -->
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">2. Tempel Deskripsi Lowongan</label>
                                    <textarea name="job_desc" rows="8" class="w-full p-4 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition resize-none" placeholder="Contoh: Dicari Laravel Developer dengan pengalaman 2 tahun..." required></textarea>
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

</body>
</html>