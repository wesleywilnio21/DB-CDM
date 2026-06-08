# Panduan Deploy Laravel di Zeabur (Gratis & Tanpa Kartu Kredit)

Zeabur adalah platform PaaS modern (seperti Railway dan Render) yang memungkinkan Anda men-deploy aplikasi Laravel langsung dari GitHub. Keunggulan utama Zeabur adalah **tidak memerlukan verifikasi kartu kredit** untuk menggunakan paket gratisnya.

---

## Prasyarat
1. Akun **GitHub** dengan repositori proyek `DB-CDM` yang sudah di-push.
2. Akun **Zeabur** (Daftar menggunakan GitHub di [zeabur.com](https://zeabur.com)).

---

## Langkah 1: Hubungkan GitHub ke Zeabur
1. Masuk ke dashboard Zeabur.
2. Klik tombol **"Create Project"** (Buat Proyek).
3. Pilih wilayah server terdekat (misal: *Singapore* atau *Tokyo* untuk kecepatan akses terbaik dari Indonesia).
4. Di dalam dashboard project baru tersebut, klik **"Deploy New Service"** (Deploy Layanan Baru) -> pilih **"GitHub"**.
5. Berikan izin akses ke akun GitHub Anda, lalu pilih repositori proyek `DB-CDM` Anda.
6. Zeabur akan otomatis mendeteksi bahwa proyek Anda menggunakan Dockerfile yang telah kita buat, atau mendeteksi sebagai proyek PHP Laravel (Nixpacks) secara otomatis.

---

## Langkah 2: Hubungkan Database (Supabase / Zeabur)
Anda memiliki dua opsi: menggunakan database **Supabase** yang sudah Anda buat sebelumnya (Sangat Direkomendasikan) atau membuat database baru langsung di Zeabur.

### Opsi A: Menggunakan Database Supabase yang Sudah Ada (Rekomendasi)
Anda tidak perlu membuat database baru di Zeabur. Cukup gunakan detail koneksi database Supabase yang sudah Anda dapatkan sebelumnya:
- **Host:** Alamat host dari Supabase (misalnya `aws-0-ap-southeast-1.pooler.supabase.com`)
- **Database:** `postgres`
- **Username:** `postgres`
- **Password:** Password database Supabase Anda
- **Port:** `5432` atau `6543`

### Opsi B: Membuat Database Baru di Zeabur
Jika Anda ingin database yang berada dalam satu proyek Zeabur:
1. Di dalam project dashboard Zeabur, klik **"Deploy New Service"** (atau ikon plus `+`).
2. Pilih **"Prebuilt Marketplace"** -> pilih **PostgreSQL** atau **MySQL**.
3. Setelah dideploy, klik layanan database tersebut dan buka tab **"Instruction"** atau **"Variables"** untuk menyalin detail koneksinya.

---

## Langkah 3: Konfigurasi Environment Variables di Zeabur
Kembali ke layanan aplikasi Laravel Anda di dashboard Zeabur, pilih tab **"Variables"** (Anda bisa menggunakan **Raw Editor** untuk menambahkan secara cepat) dan tambahkan variabel-variabel lingkungan berikut:

| Key | Value / Referensi |
| :--- | :--- |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | *(Salin dari file `.env` lokal Anda, misal: `base64:xxx...`)* |
| `DB_CONNECTION` | `pgsql` |
| `DB_HOST` | *(Host PostgreSQL Supabase / Zeabur Anda)* |
| `DB_PORT` | `5432` |
| `DB_DATABASE` | *(Database name Supabase (`postgres`) / Zeabur Anda)* |
| `DB_USERNAME` | *(Username database Supabase (`postgres`) / Zeabur Anda)* |
| `DB_PASSWORD` | *(Password database Supabase / Zeabur Anda)* |
| `LOG_CHANNEL` | `stderr` |

*Tips: Zeabur mendukung referensi otomatis variabel antar layanan di project yang sama. Jadi Anda bisa mengklik tombol untuk menghubungkan database secara otomatis.*

---

## Langkah 4: Jalankan Migrasi Database di Zeabur
Untuk menjalankan migrasi database (`php artisan migrate --force`), Anda memiliki dua cara di Zeabur:
1. **Melalui Console:** Buka layanan web Laravel Anda di Zeabur, masuk ke tab **"Console"**, lalu ketik:
   ```bash
   php artisan migrate --force
   ```
2. **Melalui Custom Start Command:** Di tab **"Settings"** pada web service Anda, cari bagian **Start Command** dan isi dengan:
   ```bash
   php artisan migrate --force && php artisan config:cache && php artisan route:cache && supervisord -c /etc/supervisor/conf.d/supervisord.conf
   ```
