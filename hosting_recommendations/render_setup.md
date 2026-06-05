# Panduan Deploy Laravel di Render (Gratis)

Render menyediakan **Free Tier** (Layanan Gratis) yang sangat cocok untuk showcase proyek Laravel Anda tanpa biaya sepeser pun.

## Yang Didapatkan dari Akun Gratis Render:
1. **Web Service Gratis:** Untuk menjalankan kode program Laravel Anda.
2. **PostgreSQL Database Gratis:** Untuk database (aktif selama 90 hari, setelah itu data harus di-backup/migrate ulang jika ingin terus gratis).
3. **SSL/HTTPS Gratis** secara otomatis.

---

## Langkah 1: Siapkan Database PostgreSQL Gratis (Tanpa Kartu Kredit)
Karena database PostgreSQL bawaan Render membutuhkan verifikasi kartu kredit/debit untuk mencegah penyalahgunaan, solusi terbaiknya adalah menggunakan penyedia database PostgreSQL cloud gratis pihak ketiga yang **tidak memerlukan kartu kredit**, seperti **Neon** (neon.tech) atau **Supabase** (supabase.com).

### Opsi A: Menggunakan Neon (Sangat Direkomendasikan & Cepat)
1. Buka [Neon.tech](https://neon.tech/) dan daftarkan akun menggunakan GitHub Anda (Gratis).
2. Setelah masuk, buat proyek baru:
   - **Project Name:** `db-cdm`
   - **Database Name:** `neondb` (atau biarkan default)
   - **Region:** Pilih **Singapore** (agar dekat dengan server Render di Asia)
3. Klik **"Create Project"**.
4. Anda akan langsung diberikan string koneksi (*Connection String*). Pilih tab **".env"** di dashboard Neon untuk melihat detail variabel lingkungan Anda. Detailnya akan terlihat seperti ini:
   - `DATABASE_URL`: `postgresql://neondb_owner:xxxxx@ep-xxxxx.ap-southeast-1.neon.tech/neondb?sslmode=require`
5. Catat detail berikut dari string tersebut:
   - **Host:** `ep-xxxxx.ap-southeast-1.neon.tech` (bagian setelah `@` hingga sebelum `/neondb`)
   - **Database:** `neondb` (bagian setelah `/` sebelum tanda tanya)
   - **Username:** `neondb_owner` (bagian setelah `postgresql://` hingga sebelum `:`)
   - **Password:** Password Anda (bagian setelah `:` hingga sebelum `@`)
   - **Port:** `5432`

### Opsi B: Menggunakan Supabase
1. Buka [Supabase.com](https://supabase.com/) dan masuk menggunakan akun GitHub Anda.
2. Klik **"New Project"** dan pilih organisasi Anda.
3. Isi konfigurasi proyek:
   - **Name:** `db-cdm`
   - **Database Password:** Buat password yang kuat dan catat.
   - **Region:** Pilih **Singapore**.
4. Setelah proyek selesai dibuat, ada dua cara mudah untuk mendapatkan informasi database:
   * **Cara Cepat (Tombol Connect):**
     - Klik tombol hijau **"Connect"** di bagian atas kanan halaman dashboard Supabase Anda.
     - Pilih **"Connection String"** -> tab **"URI"** untuk menyalin string koneksi, atau pilih **"Transaction Pooler"** / **"Session Pooler"** untuk melihat detail parameter koneksi secara terpisah.
   * **Cara Melalui Settings (Pengaturan):**
     - Klik ikon **Settings (gerigi)** di menu sidebar sebelah kiri paling bawah.
     - Pilih sub-menu **Database** (di bawah section *Project Settings*).
     - Gulir ke bawah hingga menemukan bagian **Connection Info** (atau **Connection string**).
5. Catat data berikut untuk dimasukkan ke Render nanti:
   - **Host:** Alamat host Anda (misalnya `aws-0-ap-southeast-1.pooler.supabase.com` atau sejenisnya).
   - **Port:** `5432` (atau `6543` untuk pooler).
   - **Database Name:** Biasanya secara default adalah `postgres`.
   - **User:** Secara default adalah `postgres`.
   - **Password:** Password database yang Anda buat pada langkah nomor 3.

---

## Langkah 2: Buat Dockerfile untuk Deployment di Render
Render mendukung deployment gratis menggunakan Docker. Ini adalah cara paling stabil dan direkomendasikan untuk menjalankan Laravel di Render.

Buat sebuah file bernama `Dockerfile` di root folder proyek Anda (`d:\DB-CDM\Dockerfile`) dengan isi berikut:

```dockerfile
FROM php:8.2-fpm-alpine

# Install system dependencies & PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    postgresql-dev \
    libxml2-dev \
    curl \
    zip \
    unzip \
    git \
    oniguruma-dev

RUN docker-php-ext-install pdo pdo_pgsql bcmath

# Setup working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Setup Nginx & Supervisor Configuration
COPY ./docker/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
```

*(Catatan: Anda juga dapat menggunakan opsi deteksi otomatis Native PHP milik Render, tetapi menggunakan Dockerfile memastikan versi PHP dan dependensi PostgreSQL terinstall dengan sempurna).*

---

## Langkah 3: Deploy Web Service di Render
1. Klik **"New"** -> **"Web Service"**.
2. Hubungkan akun GitHub Anda dan pilih repository `DB-CDM`.
3. Isi detailnya:
   - **Name:** `db-cdm`
   - **Language:** Pilih `Docker` (jika menggunakan Dockerfile di atas) atau `PHP` (jika ingin mencoba setup manual).
   - **Instance Type:** Pilih **Free**.
4. Di bagian **Environment Variables**, klik **Add Environment Variable** dan masukkan detail berikut:
   - `APP_ENV`: `production`
   - `APP_DEBUG`: `false`
   - `APP_KEY`: *(Salin dari file `.env` lokal)*
   - `DB_CONNECTION`: `pgsql`
   - `DB_HOST`: *(Ambil dari Host PostgreSQL Neon/Supabase Anda)*
   - `DB_PORT`: `5432`
   - `DB_DATABASE`: *(Ambil dari Database Name Neon/Supabase Anda)*
   - `DB_USERNAME`: *(Ambil dari Username Neon/Supabase Anda)*
   - `DB_PASSWORD`: *(Ambil dari Password Neon/Supabase Anda)*
5. Klik **"Create Web Service"**.

---

## Kekurangan Akun Gratis Render (Harap Diperhatikan):
- **Cold Start (Tidur Otomatis):** Jika web Anda tidak diakses selama 15 menit, Render akan menonaktifkannya sementara untuk menghemat resource server. Ketika teman Anda mengakses URL tersebut pertama kali, website akan terasa "loading lama" (sekitar 30 detik) karena server sedang dinyalakan kembali. Setelah menyala, kecepatan akses akan kembali normal.
- **Batas Database Gratis:** Jika menggunakan **Neon**, database akan tetap aktif selamanya (memiliki batas penyimpanan 0.5 GB yang sudah sangat cukup untuk showcase). Jika menggunakan **Supabase**, proyek gratis Anda akan otomatis masuk ke mode *pause* jika tidak ada aktivitas selama 1 minggu (bisa dinyalakan kembali secara manual kapan saja dengan 1 klik lewat dashboard Supabase tanpa ada kehilangan data). Ini jauh lebih baik dibanding PostgreSQL gratis Render bawaan yang terhapus permanen setelah 90 hari.
