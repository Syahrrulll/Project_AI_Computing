<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Analisis - AI Career Assistant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800">

    <div class="max-w-5xl mx-auto px-6 py-10">
        <a href="{{ route('home') }}" class="text-indigo-600 hover:underline mb-6 inline-block font-medium">&larr; Kembali ke Upload</a>

        <!-- Score Card -->
        <div class="bg-white rounded-3xl p-8 shadow-xl border border-slate-100 mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-50 rounded-full -mr-16 -mt-16 opacity-50"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex-1">
                    <h2 class="text-3xl font-bold text-slate-900 mb-2">Analisis Kecocokan</h2>
                    <p class="text-slate-600 text-lg leading-relaxed">{{ $data['summary'] ?? 'Ringkasan tidak tersedia.' }}</p>
                </div>
                
                <div class="flex items-center justify-center bg-white p-2 rounded-full shadow-lg">
                    <div class="relative w-32 h-32 flex items-center justify-center rounded-full border-8 {{ ($data['score'] ?? 0) >= 70 ? 'border-green-500' : 'border-amber-500' }}">
                        <div class="text-center">
                            <span class="block text-4xl font-extrabold text-slate-800">{{ $data['score'] ?? 0 }}</span>
                            <span class="text-xs font-bold text-slate-400 uppercase">Skor</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid md:grid-cols-2 gap-6 mb-10">
            <!-- Strengths -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-green-500">
                <h3 class="font-bold text-lg text-slate-800 mb-4 flex items-center">
                    <span class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center mr-3">✓</span>
                    Kekuatan Utama
                </h3>
                <ul class="space-y-3">
                    @foreach($data['strengths'] ?? [] as $item)
                        <li class="flex items-start text-slate-600 text-sm">
                            <span class="mr-2 text-green-500 mt-1">●</span> {{ $item }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Weaknesses / Suggestions -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-amber-500">
                <h3 class="font-bold text-lg text-slate-800 mb-4 flex items-center">
                    <span class="w-8 h-8 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center mr-3">⚡</span>
                    Saran Perbaikan
                </h3>
                <ul class="space-y-3">
                    @foreach($data['suggestions'] ?? [] as $item)
                        <li class="flex items-start text-slate-600 text-sm">
                            <span class="mr-2 text-amber-500 mt-1">➜</span> {{ $item }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- CTA Interview -->
        <div class="text-center bg-indigo-900 rounded-2xl p-8 text-white shadow-2xl">
            <h3 class="text-2xl font-bold mb-2">Siap untuk Tahap Selanjutnya?</h3>
            <p class="text-indigo-200 mb-6">Uji kemampuan menjawab pertanyaan teknis berdasarkan Job Desc ini.</p>
            
            <a href="{{ route('interview.start') }}" class="inline-flex items-center bg-white text-indigo-900 px-8 py-4 rounded-full font-bold shadow-lg hover:bg-indigo-50 transition transform hover:-translate-y-1">
                Mulai Simulasi Wawancara
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
            </a>
        </div>
    </div>

</body>
</html>