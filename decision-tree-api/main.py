"""
Decision Tree API untuk Sistem Pakar Rekomendasi Topik Penelitian
Menggunakan FastAPI dan scikit-learn DecisionTreeClassifier (CART)
"""

from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, Field
import pickle
import os
from pathlib import Path
from model import DecisionTreeModel

app = FastAPI(
    title="Decision Tree API - Sistem Pakar",
    description="API untuk prediksi area riset berdasarkan nilai mata kuliah",
    version="1.0.0"
)

# CORS middleware untuk mengizinkan request dari Laravel
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Dalam production, ganti dengan domain Laravel spesifik
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Load model saat startup
model = None
MODEL_PATH = Path(__file__).parent / "models" / "decision_tree_model.pkl"

@app.on_event("startup")
async def load_model():
    """Load model decision tree saat aplikasi start"""
    global model
    try:
        if MODEL_PATH.exists():
            with open(MODEL_PATH, 'rb') as f:
                model = pickle.load(f)
            print(f"Model loaded successfully from {MODEL_PATH}")
        else:
            print(f"Model file not found at {MODEL_PATH}. Training new model...")
            model = DecisionTreeModel()
            model.train()
            model.save(MODEL_PATH)
            print("New model trained and saved.")
    except Exception as e:
        print(f"Error loading model: {e}")
        # Fallback: train new model
        model = DecisionTreeModel()
        model.train()
        model.save(MODEL_PATH)


class PredictionRequest(BaseModel):
    """Request model untuk prediksi"""
    algoritma: float = Field(..., ge=0, le=100, description="Nilai mata kuliah Algoritma & Struktur Data")
    pemrograman: float = Field(..., ge=0, le=100, description="Nilai mata kuliah Pemrograman")
    basis_data: float = Field(..., ge=0, le=100, description="Nilai mata kuliah Basis Data")
    kecerdasan_buatan: float = Field(..., ge=0, le=100, description="Nilai mata kuliah Kecerdasan Buatan")


class PredictionResponse(BaseModel):
    """Response model untuk prediksi"""
    kode_area: str = Field(..., description="Kode area riset yang diprediksi")
    area_nama: str = Field(..., description="Nama area riset")
    confidence: float = Field(..., ge=0, le=1, description="Tingkat kepercayaan prediksi (0-1)")


@app.get("/api/health")
async def health_check():
    """Health check endpoint"""
    return {
        "status": "healthy",
        "model_loaded": model is not None
    }


@app.post("/api/predict", response_model=PredictionResponse)
async def predict(request: PredictionRequest):
    """
    Prediksi area riset berdasarkan nilai mata kuliah
    
    Input: Nilai 4 mata kuliah kunci (0-100)
    Output: Kode area riset yang diprediksi beserta confidence score
    """
    if model is None:
        raise HTTPException(status_code=503, detail="Model belum dimuat. Silakan tunggu beberapa saat.")
    
    try:
        # Format data untuk model
        input_data = [[
            request.algoritma,
            request.pemrograman,
            request.basis_data,
            request.kecerdasan_buatan
        ]]
        
        # Prediksi
        prediction = model.predict(input_data)
        probabilities = model.predict_proba(input_data)
        
        # Ambil kode_area yang diprediksi
        kode_area = prediction[0]
        
        # Hitung confidence (probabilitas tertinggi)
        max_probability = probabilities[0].max()
        
        # Mapping kode_area ke nama (bisa dipindah ke database/config)
        area_names = {
            'WEB_MOBILE': 'Pengembangan Web dan Mobile',
            'GAME_DEV': 'Pengembangan Game',
            'EXPERT_SYSTEM': 'Sistem Pakar',
            'NLP': 'Pemrosesan Bahasa Alami (NLP)',
            'CYBER_SEC': 'Keamanan Siber',
            'DATA_ANALYTICS': 'Analitika Data dan Business Intelligence',
        }
        
        area_nama = area_names.get(kode_area, kode_area)
        
        return PredictionResponse(
            kode_area=kode_area,
            area_nama=area_nama,
            confidence=float(max_probability)
        )
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Error saat prediksi: {str(e)}")


@app.post("/api/train")
async def train_model():
    """
    Retrain model dengan data terbaru
    Endpoint ini bisa digunakan oleh admin untuk update model
    """
    try:
        global model
        model = DecisionTreeModel()
        model.train()
        model.save(MODEL_PATH)
        return {
            "status": "success",
            "message": "Model berhasil dilatih ulang",
            "model_path": str(MODEL_PATH)
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Error saat training: {str(e)}")


if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8001)

