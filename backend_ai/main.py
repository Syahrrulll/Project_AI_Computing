import os
import re
import json
import httpx
import uvicorn
from typing import List, Dict, Any, Optional
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel

# ======================================================================
# CONFIG & CREDENTIALS
# ======================================================================
app = FastAPI(title="CV & Interview AI Engine (Chutes Version)", version="2.1")

# API Key & Model (Tetap sama)
CHUTES_API_KEY = os.getenv(
    "CHUTES_API_KEY",
    "cpk_49d03a0e918f44c5b753d8aefa411eb0.0140b8ee2e8c5bfbae7e6bc921a677ba.VYnSymDVRjdpY53MK4NduBfyff9RKdoD"
)
CHUTES_API_URL = "https://llm.chutes.ai/v1/chat/completions"
MODEL_NAME = "moonshotai/Kimi-K2-Instruct-0905"

# ======================================================================
# AI HELPER FUNCTIONS
# ======================================================================
async def call_ai_chat(messages: List[Dict[str, str]], max_tokens: Optional[int] = None) -> Dict[str, Any]:
    """Mengirim request ke Chutes API."""
    headers = {
        "Authorization": f"Bearer {CHUTES_API_KEY}",
        "Content-Type": "application/json"
    }

    payload: Dict[str, Any] = {
        "model": MODEL_NAME,
        "messages": messages,
        "temperature": 0.5
    }
    if max_tokens:
        payload["max_tokens"] = max_tokens

    async with httpx.AsyncClient(timeout=60.0) as client:
        try:
            resp = await client.post(CHUTES_API_URL, json=payload, headers=headers)
            resp.raise_for_status()
            return resp.json()
        except httpx.HTTPStatusError as e:
            raise HTTPException(status_code=resp.status_code, detail=f"AI provider error: {resp.text}")
        except Exception as e:
            raise HTTPException(status_code=500, detail=f"Connection error: {str(e)}")

async def call_ai_json(system_prompt: str, user_prompt: str, expect_json: bool = True) -> Any:
    """Wrapper untuk memanggil AI."""
    messages = []
    if system_prompt:
        messages.append({"role": "system", "content": system_prompt})
    messages.append({"role": "user", "content": user_prompt})

    resp = await call_ai_chat(messages, max_tokens=1500)

    content_text = None
    try:
        choice = resp.get("choices", [])[0]
        content_text = choice["message"].get("content")
    except Exception:
        raise HTTPException(status_code=500, detail="Format respon AI tidak valid.")

    if not expect_json:
        return content_text

    try:
        cleaned_text = content_text.replace("```json", "").replace("```", "").strip()
        return json.loads(cleaned_text)
    except json.JSONDecodeError:
        m = re.search(r'(\{.*\}|\[.*\])', content_text, flags=re.DOTALL)
        if m: return json.loads(m.group(1))
        raise HTTPException(status_code=500, detail=f"Gagal parsing JSON. Raw: {content_text[:200]}...")

# ======================================================================
# DATA MODELS
# ======================================================================
class AnalyzeRequest(BaseModel):
    cv_text: str
    job_desc: str

class ChatRequest(BaseModel):
    history: List[Dict[str, Any]] = []
    message: str
    context: str

# ======================================================================
# ENDPOINTS
# ======================================================================

@app.get("/")
def health_check():
    return {"status": "running", "persona": "Friendly HRD"}
    print("Test Running")

@app.post("/analyze-cv")
async def analyze_cv(request: AnalyzeRequest):
    """
    Analisis CV dengan gaya Career Coach yang suportif.
    """
    system_prompt = (
        "Bertindaklah sebagai Senior Career Coach yang suportif dan teliti. "
        "Analisis kecocokan antara CV dan Job Description. "
        "Gunakan bahasa Indonesia yang profesional namun mudah dipahami. "
        "Berikan output HANYA dalam format JSON valid dengan key: "
        "score (integer 0-100), summary (string kalimat naratif), strengths (array), "
        "weaknesses (array), suggestions (array saran konkret)."
    )
    
    user_prompt = f"JOB: {request.job_desc}\nCV: {request.cv_text}"

    return await call_ai_json(system_prompt, user_prompt, expect_json=True)

@app.post("/interview/start")
async def start_interview(request: AnalyzeRequest):
    """
    Pertanyaan pembuka yang lebih ramah.
    """
    system_prompt = (
        "Anda adalah HRD Manager yang ramah, hangat, dan profesional. "
        "Tugas Anda adalah menyapa kandidat dengan luwes (seperti manusia), "
        "lalu memberikan SATU pertanyaan pembuka yang relevan dengan posisi yang dilamar. "
        "JANGAN gunakan penomoran. Gunakan bahasa percakapan yang natural."
    )
    
    user_prompt = f"Konteks Job: {request.job_desc}\nCV Kandidat: {request.cv_text}"

    text_response = await call_ai_json(system_prompt, user_prompt, expect_json=False)
    return {"message": text_response}

@app.post("/interview/chat")
async def chat_interview(request: ChatRequest):
    """
    Chat Wawancara dengan persona yang lebih luwes & natural.
    """
    system_prompt = f"""
    Anda adalah HRD Manager senior yang sedang mewawancarai kandidat.
    
    [PERSONA & GAYA BICARA]
    1. Bersikaplah ramah, suportif, namun tetap profesional (seperti wawancara tatap muka).
    2. Gunakan Bahasa Indonesia yang mengalir dan natural (luwes). HINDARI gaya bahasa kaku/robotik.
    3. Jangan ragu memberikan apresiasi singkat (misal: "Menarik sekali", "Jawaban yang bagus") sebelum bertanya lagi.
    4. Kalau ditanya terkait hal yang tidak cocok dengan topik, jawab saja tidak tahu karena anda adalah HRD yang profesional dan tegas, lalu setelah menjawab tidak,  ajak kembali ke topik utama (Wawancara)
    
    [KONTEKS DOKUMEN]
    {request.context}
    
    [INSTRUKSI INTERAKSI]
    1. Evaluasi jawaban kandidat. Jika terlalu singkat/kurang jelas, minta elaborasi dengan sopan (probing).
    2. Jika jawaban sudah cukup, gali aspek lain yang relevan dengan Job Description.
    3. Jaga respon tetap ringkas (maksimal 2-3 kalimat utama) agar percakapan terus berjalan dua arah.
    """

    messages = [{"role": "system", "content": system_prompt}]
    
    # Reconstruct History
    for msg in request.history:
        role = msg.get("role", "user")
        content = ""
        if "parts" in msg and isinstance(msg["parts"], list): content = " ".join(msg["parts"])
        elif "content" in msg: content = msg["content"]
        
        if role == "model": role = "assistant"
        if content: messages.append({"role": role, "content": content})

    messages.append({"role": "user", "content": request.message})

    try:
        resp = await call_ai_chat(messages, max_tokens=500)
        reply = resp["choices"][0]["message"]["content"]
        return {"reply": reply}
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

if __name__ == "__main__":
    uvicorn.run(app, host="127.0.0.1", port=8001)