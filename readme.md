# 🚀 AI Career Assistant  
**Sistem Optimalisasi CV & Simulasi Wawancara Berbasis AI**  

---

## 📖 Tentang Proyek  

AI Career Assistant adalah platform berbasis web cerdas yang dirancang untuk membantu pelamar kerja — terutama fresh graduate — mempersiapkan diri menghadapi dunia kerja secara lebih efektif.  

Sistem ini menganalisis **CV (PDF)** pengguna dan membandingkannya dengan **Deskripsi Lowongan Kerja (Job Description)** menggunakan teknologi kecerdasan buatan (AI). Selain memberikan analisis mendalam, aplikasi ini juga menyediakan **simulasi wawancara interaktif** dan **pembuatan surat lamaran otomatis** yang dipersonalisasi.  

---

## 🌟 Manfaat & Keunggulan  

Proyek ini hadir untuk menjawab tantangan umum yang dihadapi pelamar kerja:  

✅ **Lolos Seleksi Administrasi (ATS)**  
   - Analisis kata kunci dan struktur CV agar sesuai dengan sistem screening perusahaan.  

✅ **Persiapan Mental Lewat Simulasi**  
   - Latihan wawancara real-time dengan AI yang berperan sebagai HRD.  

✅ **Efisiensi Waktu**  
   - Hasilkan **Cover Letter** profesional dalam hitungan detik, tanpa mengetik manual.  

✅ **Feedback Objektif & Terukur**  
   - Dapatkan **skor kecocokan (0-100%)** dan saran perbaikan konkret berbasis data.  

---

## 🏗️ Arsitektur Sistem  

Aplikasi ini dibangun dengan arsitektur **decoupled (terpisah)** untuk performa dan skalabilitas optimal:  

### **Frontend (Client-side)**  
- **Framework**: Laravel 10/11 (PHP)  
- **Styling**: Tailwind CSS  
- **PDF Parser**: smalot/pdfparser  
- **HTTP Client**: Laravel HTTP Facade (Guzzle)  

### **Backend (AI Engine)**  
- **Bahasa**: Python 3.9+  
- **Framework API**: FastAPI  
- **Server**: Uvicorn  
- **AI Model**:  
  - Chutes API (Model: moonshotai/Kimi-K2-Instruct)  
  - Opsional: Google Gemini API  

---

## ⚙️ Prasyarat & Instalasi  

### **Prasyarat Sistem**  
Pastikan perangkat Anda telah terinstal:  

- PHP ≥ 8.1 & Composer  
- Python ≥ 3.9 & PIP  
- Git  

### **Langkah Instalasi**  

1. **Clone Repository**  
   ```bash
   git clone https://github.com/Syahrrulll/Project_AI_Computing.git
   cd Project_AI_Computing
   ```

2. **Setup Frontend (Laravel)**  
   ```bash
   cd ./frontend_web
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan serve
   ```


---

## 🚀 Panduan Penggunaan  

### **Langkah 1: Analisis CV**  
1. Buka aplikasi di `http://127.0.0.1:8000`.  
2. Upload **CV Anda (format PDF)**.  
3. Tempelkan **teks deskripsi lowongan** dari LinkedIn/JobStreet.  
4. Klik **"Mulai Analisis AI"** dan tunggu proses selesai.  

### **Langkah 2: Tinjau Hasil**  
- Lihat **Skor Kecocokan** (contoh: 85/100).  
- Baca **Kekuatan** yang sesuai dengan lowongan.  
- Perhatikan **Saran Perbaikan** untuk meningkatkan CV.  

### **Langkah 3: Fitur Lanjutan**  
Pada halaman hasil, tersedia dua opsi:  

📄 **Buat Surat Lamaran Otomatis**  
   - AI akan menuliskan cover letter formal berdasarkan CV dan lowongan.  

💬 **Mulai Simulasi Wawancara**  
   - Masuk ke mode chat interaktif.  
   - AI akan berperan sebagai HRD dan mengajukan pertanyaan wawancara.  
   - Jawab seperti wawancara nyata untuk melatih respons Anda.  

---

## 🧪 Testing  

### **Backend (FastAPI)**  
```bash
cd backend
pytest
```

### **Frontend (Laravel)**  
```bash
cd frontend_web
php artisan test
```

---

## 📁 Struktur Proyek  

```
ai-career-assistant/
├── backend_ai/                 # AI Engine (Python/FastAPI)
│   ├── main.py             # Entry point API
│   └── requirements.txt
├── frontend_web/               # Web App(Laravel)
│   ├── app/Http/Controllers/
│   ├── resources/views/
│   └── composer.json
├── .gitignore
└── README.md
```

---

## 🔧 Teknologi Pendukung  

| Komponen         | Teknologi                     |
|------------------|-------------------------------|
| Frontend         | Laravel, Tailwind, JavaScript |
| Backend AI       | FastAPI, Uvicorn              |
| AI Model         | Chutes API, Google Gemini     |
| PDF Processing   | pdfparser                     |
| HTTP Client      | Guzzle (via Laravel HTTP)     |


---


