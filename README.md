<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>
</p>

# Sistem Pakar Rekomendasi Topik Penelitian

Repositori ini berisi kode sumber untuk memenuhi skripsi berjudul "Pengembangan Sistem Pakar Rekomendasi Topik Penelitian Dengan Kombinasi Rule Base Dan Decision Tree".
Sistem ini dirancang untuk membantu mahasiswa dalam menemukan topik penelitian yang sesuai dengan kemampuan dan minat mereka.

&nbsp;

## 🚀 Instalasi dan Prasyarat

Repo ini memerlukan beberapa resource berikut ini untuk menjalankan proyek ini secara lokal.

- PHP (versi 8.3 atau lebih baru)

- Composer

- Node.js & NPM

- Server Database MySQL

### Langkah-Langkah Instalasi

1).  Clone proyek ke repo 

```bash
  git clone https://github.com/UlhaqDaffa/sistem-pakar-skripsi.git
```

2). Pergi ke direktori proyek

```bash
  cd sistem-pakar
```

3). Install dependensi PHP (Composer)

```bash
  composer install
```

4). Install dependensi JavaScript (NPM)

```bash
  npm install
```

5). Buat file `.env` dari contoh

```bash
  cp .env.example .env
```

6). Generate kunci aplikasi Laravel

```bash
  php artisan key:generate
```

7). Konfigurasi koneksi database di dalam file `.env`

```javascript
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=sistempakar
  DB_USERNAME=root
  DB_PASSWORD=
```

8). Jalankan migrasi database untuk membuat tabel-tabel yang diperlukan

```bash
  php artisan migrate
```

9). Jalankan proses build aset frontend

```bash
  npm run dev
```

10). Jalankan server development

```bash
  composer run dev
```

&nbsp;


## 🛠️ Dibangun Dengan

* **Backend**: [Laravel](https://laravel.com/) dan [FastAPI](https://fastapi.tiangolo.com/) 
* **Frontend**: [Livewire](https://livewire.laravel.com/) + [Volt](https://livewire.laravel.com/docs/volt), [Tailwind](https://tailwindcss.com/), [Alpine.js](https://alpinejs.dev/)
* **Komponen UI**: [Flux](https://fluxui.dev/)
* **Database**: [MySQL](https://www.mysql.com/)
* **ML**: [Python](https://www.python.org/)

