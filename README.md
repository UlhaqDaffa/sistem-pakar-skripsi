
# Sistem Pakar Rekomendasi Topik Penelitian

Repositori ini berisi kode sumber untuk proyek skripsi berjudul "Pengembangan Sistem Pakar Rekomendasi Topik Penelitian Dengan Kombinasi Rule Base Dan Decision Tree".
Sistem ini dirancang untuk membantu mahasiswa dalam menemukan topik penelitian yang sesuai dengan kemampuan dan minat mereka.

&nbsp;

## Tentang Sistem Pakar Rule-Based+Decision Tree

Sistem pakar ini bertujuan untuk memberikan rekomendasi topik penelitian kepada mahasiswa. Keunikannya terletak pada penggunaan dua metode yang digabungkan, yaitu Rule-Based (berbasis aturan) dan Decision Tree (pohon keputusan).

Proses kerjanya dibagi menjadi dua tahap utama (filter-then-classify):

- Tahap Filter (Rule-Based) 

  Sistem akan terlebih dahulu menyaring (mem-filter) data kemampuan mahasiswa. Aturan-aturan ini didasarkan pada pengetahuan dari para pakar seperti dosen. Misalnya, aturan bisa berdasarkan nilai mata kuliah tertentu, minat, atau keahlian teknis yang dimiliki mahasiswa.

- Tahap Klasifikasi (Decision Tree)

    Hasil dari tahap filter (yang sudah diubah menjadi format angka) kemudian akan diolah oleh model Decision Tree dengan algoritma CART. Model ini dilatih menggunakan data historis dari mahasiswa-mahasiswa yang sudah lulus. Tujuannya adalah untuk menemukan pola dan memberikan rekomendasi topik yang paling relevan berdasarkan "pengalaman" dari data tersebut.

&nbsp;

## 🚀 Instalasi dan Prasyarat

Repo ini memerlukan beberapa resource berikut ini untuk menjalankan proyek ini secara lokal.

- PHP (versi 8.1 atau lebih baru)

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

* **Backend**: [Laravel](https://laravel.com/)
* **Frontend**: [Livewire](https://livewire.laravel.com/) + [Volt](https://livewire.laravel.com/docs/volt), [Tailwind CSS](https://tailwindcss.com/), [Alpine.js](https://alpinejs.dev/)
* **Komponen UI**: [Flux]([https://facebook.github.io/flux/](https://fluxui.dev/))
* **Database**: [MySQL](https://www.mysql.com/)
