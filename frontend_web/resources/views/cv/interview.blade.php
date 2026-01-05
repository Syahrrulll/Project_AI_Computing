<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Interview - AI HRD</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-slate-100 h-screen flex flex-col overflow-hidden">

    <!-- Header -->
    <div class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center z-10 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xl">🤖</div>
            <div>
                <h1 class="font-bold text-slate-800 leading-tight">AI Interviewer</h1>
                <div class="flex items-center text-xs text-green-500 font-medium">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></span> Online
                </div>
            </div>
        </div>
        <a href="{{ route('home') }}" class="text-sm font-medium text-red-500 hover:text-red-700 hover:bg-red-50 px-3 py-2 rounded-lg transition">Akhiri Sesi</a>
    </div>

    <!-- Chat Container -->
    <div id="chat-container" class="flex-1 overflow-y-auto p-4 md:p-6 space-y-6 scroll-smooth">
        <!-- AI Initial Message -->
        <div class="flex w-full mt-2 space-x-3 max-w-3xl mx-auto">
            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-bold">AI</div>
            <div class="bg-white p-4 rounded-2xl rounded-tl-none shadow-sm text-slate-700 border border-slate-100">
                {{ $firstQuestion }}
            </div>
        </div>
    </div>

    <!-- Input Area -->
    <div class="bg-white border-t border-slate-200 p-4">
        <div class="max-w-3xl mx-auto">
            <form id="chat-form" class="relative flex items-center gap-2">
                <input type="text" id="user-input" 
                    class="w-full bg-slate-100 text-slate-800 border-0 rounded-full px-6 py-4 pr-12 focus:ring-2 focus:ring-indigo-500 focus:outline-none placeholder-slate-400" 
                    placeholder="Ketik jawaban Anda di sini..." autocomplete="off">
                
                <button type="submit" 
                    class="absolute right-2 p-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full shadow-md transition-all transform hover:scale-105 flex items-center justify-center w-10 h-10">
                    <svg class="w-5 h-5 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </form>
            <p class="text-center text-xs text-slate-400 mt-2">AI dapat membuat kesalahan. Cek kembali informasi penting.</p>
        </div>
    </div>

    <!-- JavaScript Logic -->
    <script>
        const chatContainer = document.getElementById('chat-container');
        const form = document.getElementById('chat-form');
        const input = document.getElementById('user-input');

        // Fungsi Auto Scroll ke Bawah
        function scrollToBottom() {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        // Fungsi Render Pesan
        function appendMessage(role, text) {
            const isUser = role === 'user';
            const div = document.createElement('div');
            div.className = `flex w-full space-x-3 max-w-3xl mx-auto ${isUser ? 'justify-end' : ''}`;
            
            div.innerHTML = `
                ${!isUser ? '<div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-bold">AI</div>' : ''}
                <div class="${isUser ? 'bg-indigo-600 text-white rounded-tr-none' : 'bg-white text-slate-700 rounded-tl-none border border-slate-100'} p-4 rounded-2xl shadow-sm max-w-[85%] md:max-w-[75%] leading-relaxed">
                    ${text}
                </div>
                ${isUser ? '<div class="flex-shrink-0 h-8 w-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 text-xs font-bold">ME</div>' : ''}
            `;
            
            chatContainer.appendChild(div);
            scrollToBottom();
        }

        // Fungsi Render Loading Indicator
        function showLoading() {
            const id = 'loading-' + Date.now();
            const div = document.createElement('div');
            div.id = id;
            div.className = 'flex w-full space-x-3 max-w-3xl mx-auto mt-4';
            div.innerHTML = `
                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-bold">AI</div>
                <div class="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm border border-slate-100 flex items-center gap-1">
                    <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce"></span>
                    <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce delay-75"></span>
                    <span class="w-2 h-2 bg-slate-400 rounded-full animate-bounce delay-150"></span>
                </div>
            `;
            chatContainer.appendChild(div);
            scrollToBottom();
            return id;
        }

        // Handle Submit
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = input.value.trim();
            if (!message) return;

            // 1. Tampilkan pesan user
            appendMessage('user', message);
            input.value = '';
            input.disabled = true;

            // 2. Tampilkan loading
            const loadingId = showLoading();

            try {
                // 3. Kirim ke Backend Laravel
                const response = await axios.post("{{ route('interview.chat') }}", {
                    message: message,
                    _token: "{{ csrf_token() }}"
                });

                // 4. Hapus loading & tampilkan balasan AI
                document.getElementById(loadingId).remove();
                appendMessage('ai', response.data.reply);

            } catch (error) {
                document.getElementById(loadingId).remove();
                alert('Maaf, terjadi kesalahan koneksi.');
                console.error(error);
            } finally {
                input.disabled = false;
                input.focus();
            }
        });
    </script>

</body>
</html>