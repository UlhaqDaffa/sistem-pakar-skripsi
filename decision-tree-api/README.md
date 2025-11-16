# Decision Tree API - Sistem Pakar Rekomendasi Topik Penelitian

API untuk prediksi area riset berdasarkan nilai mata kuliah menggunakan Decision Tree (CART algorithm).

## Instalasi

1. Install dependencies:
```bash
pip install -r requirements.txt
```

2. Pastikan direktori `data/` dan `models/` ada:
```bash
mkdir -p data models
```

3. Jalankan API:
```bash
python main.py
```

Atau menggunakan uvicorn langsung:
```bash
uvicorn main:app --host 0.0.0.0 --port 8001 --reload
```

API akan berjalan di `http://localhost:8001`

## Endpoints

### GET `/api/health`
Health check endpoint untuk memastikan API berjalan.

**Response:**
```json
{
  "status": "healthy",
  "model_loaded": true
}
```

### POST `/api/predict`
Prediksi area riset berdasarkan nilai mata kuliah.

**Request Body:**
```json
{
  "algoritma": 85,
  "pemrograman": 90,
  "basis_data": 88,
  "kecerdasan_buatan": 82
}
```

**Response:**
```json
{
  "kode_area": "WEB_MOBILE",
  "area_nama": "Pengembangan Web dan Mobile",
  "confidence": 0.85
}
```

### POST `/api/train`
Retrain model dengan data terbaru. Endpoint ini bisa digunakan oleh admin.

**Response:**
```json
{
  "status": "success",
  "message": "Model berhasil dilatih ulang",
  "model_path": "models/decision_tree_model.pkl"
}
```

## Data Training

File `data/training_data.csv` berisi data historis untuk training model. Format:

```csv
algoritma,pemrograman,basis_data,kecerdasan_buatan,kode_area
85,90,88,82,WEB_MOBILE
90,85,80,95,EXPERT_SYSTEM
...
```

**Catatan:** Jika file `training_data.csv` tidak ada, sistem akan generate dummy data untuk development. Dalam production, ganti dengan data historis yang sebenarnya.

## Model

Model menggunakan scikit-learn `DecisionTreeClassifier` dengan:
- Algorithm: CART (Gini impurity)
- Max depth: 10
- Min samples split: 5
- Min samples leaf: 2

Model disimpan di `models/decision_tree_model.pkl` dan akan otomatis di-load saat API start.

## Integrasi dengan Laravel

Set URL API di file `.env` Laravel:
```
DECISION_TREE_API_URL=http://localhost:8001
```

Laravel akan memanggil endpoint `/api/predict` dengan data nilai mata kuliah.

## Development

Untuk development, jalankan dengan auto-reload:
```bash
uvicorn main:app --reload --host 0.0.0.0 --port 8001
```

## Production

Untuk production, gunakan gunicorn atau proses manager seperti systemd/supervisor:
```bash
gunicorn main:app -w 4 -k uvicorn.workers.UvicornWorker -b 0.0.0.0:8001
```

