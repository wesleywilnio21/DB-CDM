# Panduan Deploy Laravel di Railway

Railway adalah pilihan terbaik untuk showcase karena mendukung deteksi otomatis (*auto-detection*) untuk framework Laravel. Berikut adalah panduan langkah demi langkah untuk melakukan deploy proyek **DB-CDM** ke Railway.

## Prasyarat
1. Akun **GitHub** dan proyek Anda sudah di-push ke repository GitHub (publik maupun privat).
2. Akun **Railway** (daftar menggunakan GitHub di [railway.app](https://railway.app)).

---

## Langkah 1: Hubungkan GitHub ke Railway
1. Di dashboard Railway, klik tombol **"New Project"**.
2. Pilih **"Deploy from GitHub repo"**.
3. Pilih repository proyek Laravel Anda (`DB-CDM`).
4. Klik **"Deploy Now"**.
*Catatan: Deploy pertama mungkin akan gagal atau belum berjalan sempurna karena kita belum mengonfigurasi Database dan Environment Variables.*

## Langkah 2: Setup Database MySQL di Railway
1. Di dalam project dashboard Railway Anda, klik **"New"** (tombol plus di kanan atas).
2. Pilih **"Database"** -> **"Add MySQL"**.
3. Railway akan membuat instance MySQL secara instan.
4. Klik pada kotak MySQL yang baru dibuat, buka tab **"Variables"**, lalu salin informasi koneksinya (host, port, database name, username, password).

## Langkah 3: Konfigurasi Environment Variables (Variabel Lingkungan)
Kembali ke service aplikasi Laravel Anda di Railway, masuk ke tab **"Variables"**, lalu tambahkan variabel berikut:

| Key | Value / Referensi |
| :--- | :--- |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` (set `true` jika sedang debugging setup) |
| `APP_KEY` | *(Salin dari file `.env` lokal Anda, misal: `base64:xxx...`)* |
| `APP_URL` | `${{RAILWAY_STATIC_URL}}` *(menggunakan domain otomatis dari Railway)* |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | `${{MySQL.MYSQLHOST}}` *(Referensi otomatis dari database Railway)* |
| `DB_PORT` | `${{MySQL.MYSQLPORT}}` |
| `DB_DATABASE` | `${{MySQL.MYSQLDATABASE}}` |
| `DB_USERNAME` | `${{MySQL.MYSQLUSER}}` |
| `DB_PASSWORD` | `${{MySQL.MYSQLPASSWORD}}` |
| `LOG_CHANNEL` | `stderr` *(penting agar log Laravel muncul di Railway Deploy Logs)* |

---

## Langkah 4: Penyesuaian Build & Start Command (Nixpacks)
Railway mendeteksi Laravel secara otomatis dan menggunakan web server internal. Namun, pastikan langkah-langkah berikut dikonfigurasi agar aset terkompilasi dan database bermigrasi saat deployment.

### A. Konfigurasi Migration & Optimization Otomatis
Anda bisa menambahkan perintah di file `composer.json` atau menggunakan Build/Start Command di Railway. Cara paling bersih adalah memodifikasi bagian `scripts` pada `composer.json` di proyek Anda agar menjalankan migrasi database saat deployment selesai:

Tambahkan/ubah script `post-autoload-dump` di `composer.json`:
```json
"post-autoload-dump": [
    "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
    "@php artisan package:discover --ansi"
]
```
Atau tambahkan Custom Start Command di tab **Settings** aplikasi Anda di Railway:
```bash
php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && apache2-foreground
```
*(Sesuaikan base command sesuai container yang digunakan, Nixpacks biasanya mendeteksi Nginx/Apache secara otomatis)*.

---

## Langkah 5: Hubungkan Domain
1. Masuk ke tab **Settings** pada service aplikasi Anda.
2. Di bagian **Environment**, cari bagian **Domains**.
3. Klik **"Generate Domain"** untuk mendapatkan URL gratis berakhiran `.up.railway.app` yang siap dibagikan ke teman Anda.
