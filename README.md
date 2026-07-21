# 🚢 Sistem Monitoring Maritim dan Pelabuhan Global

Sistem Monitoring Maritim dan Pelabuhan Global adalah platform aplikasi berbasis web (*web-based command center*) yang dirancang khusus untuk memantau, menganalisis, dan memvisualisasikan data operasional pelabuhan global secara *real-time*. Sistem ini menggabungkan integrasi peta geografis (GIS Map), dasbor metrik analitik, dan alat komparasi risiko negara untuk mendukung pengambilan keputusan strategis dalam rantai pasok industri logistik maritim.

---

## ✨ Fitur Utama (Features)

*   **🗺️ Peta Geografis Interaktif (Map Viewer):** Visualisasi sebaran ribuan pelabuhan global di atas peta dunia dengan fitur penanda (marker) pintar.
*   **⚖️ Komparator Data (Comparison Tool):** Membandingkan secara langsung metrik antar negara atau pelabuhan (kapasitas laut, risiko negara, stabilitas ekonomi) secara berdampingan.
*   **📊 Dasbor Analitik (Dashboard):** Ringkasan instan pergerakan metrik maritim, kurs mata uang global, dan agregasi berita terbaru.
*   **⭐ Daftar Pantauan Personal (Watchlist):** Pengguna dapat mem-bookmark pelabuhan atau negara tujuan utama untuk pengawasan harian yang terfokus.
*   **🗄️ Direktori Pelabuhan Terpusat:** Repositori data lengkap yang menyajikan lokasi spesifik pelabuhan, ketersediaan, serta kapasitas *deep-water* di seluruh benua.
*   **🛠️ Modul Admin CRUD:** Ruang kendali eksklusif (back-office) bagi administrator untuk memanajemen data pengguna, rilis artikel baru, dan pembaharuan indeks pelabuhan.

## 🛠️ Teknologi yang Digunakan (Tech Stack)

Aplikasi ini dibangun menggunakan tumpukan teknologi modern skala *Enterprise* untuk menjamin ketahanan sistem saat mengolah ratusan data operasional:
*   **Backend Framework:** Laravel (PHP)
*   **Arsitektur:** MVC (Model-View-Controller)
*   **Basis Data:** MySQL / MariaDB (via Eloquent ORM & Laravel Migrations)
*   **Frontend Engine:** HTML5, CSS3, Blade Templating Engine
*   **Data Exchange:** Asynchronous JavaScript (AJAX) untuk antarmuka interaktif tanpa memuat ulang halaman (*Single Page Experience*)
*   **Integrasi Peta:** JavaScript Mapping Library (GIS Integration)

## 📋 Prasyarat (Prerequisites)

Sebelum melakukan instalasi di komputer lokal Anda, pastikan sistem Anda telah memiliki hal-hal berikut:
*   [PHP](https://www.php.net/) (Versi >= 8.1 direkomendasikan)
*   [Composer](https://getcomposer.org/) (Manajer dependensi PHP)
*   [MySQL / MariaDB](https://www.mysql.com/) (Atau menggunakan paket semacam XAMPP/Laragon)
*   [Node.js & NPM](https://nodejs.org/) (Opsional, untuk kompilasi aset jika menggunakan Vite/Mix)

## 🚀 Panduan Instalasi (Installation Guide)

Ikuti langkah-langkah di bawah ini untuk menjalankan proyek secara lokal:

1. **Kloning Repositori ini:**
   ```bash
   git clone https://github.com/username-anda/sistem-monitoring.git
   cd sistem-monitoring
   ```

2. **Instalasi Dependensi PHP:**
   ```bash
   composer install
   ```

3. **Pengaturan *Environment* (Konfigurasi Database):**
   Salin file konfigurasi bawaan dan hasilkan kunci aplikasi (App Key).
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Buka file `.env` dan pastikan konfigurasi kredensial basis data Anda sudah sesuai (DB_DATABASE, DB_USERNAME, DB_PASSWORD).*

4. **Migrasi dan *Seeding* Database:**
   Bangun struktur pangkalan data dan isi dengan data tiruan (dummy) bawaan untuk keperluan demo.
   ```bash
   php artisan migrate:fresh --seed
   ```
   *(Atau secara alternatif, Anda bisa mengakses endpoint `/seed-data` lewat browser setelah server menyala untuk instalasi data satu kali klik).*

5. **Nyalakan Server Lokal:**
   ```bash
   php artisan serve
   ```
   Aplikasi Anda kini akan berjalan pada `http://localhost:8000`

## 👨‍💻 Kontribusi

Proyek ini sangat terbuka untuk dikembangkan! Jika Anda menemukan kutu (*bugs*) atau ingin mengusulkan penambahan fungsionalitas (seperti integrasi API Cuaca Global), silakan buka *Issue* atau kirimkan *Pull Request*.

## 📄 Lisensi

Sistem Monitoring Maritim ini dibuat bersifat *Open Source*. Namun silakan periksa berkas `LICENSE` (jika ada) untuk detail ketentuan atribusi atau komersialisasinya.
