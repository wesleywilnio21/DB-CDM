# Panduan Deploy Laravel di Hugging Face Spaces (Gratis & Bebas Kartu Kredit)

Hugging Face Spaces menyediakan hosting kontainer Docker gratis dengan spesifikasi **2 vCPU & 16GB RAM** tanpa perlu mendaftarkan kartu kredit/debit sama sekali. Sangat cocok untuk showcase aplikasi Laravel dengan database SQLite Auto-Reset.

---

## Prasyarat
1. Akun **Hugging Face** (Daftar gratis di [huggingface.co](https://huggingface.co/) menggunakan email, tanpa kartu kredit).
2. Kode proyek Anda sudah siap di komputer lokal.

---

## Langkah 1: Buat Space Baru di Hugging Face
1. Setelah login ke Hugging Face, klik profil Anda di kanan atas -> pilih **"New Space"**.
2. Isi formulir pembuatan Space:
   - **Space Name:** `db-cdm` (atau sesuaikan dengan keinginan Anda).
   - **License:** Pilih `mit` atau biarkan kosong.
   - **Select the Space SDK:** Pilih **Docker**.
   - **Docker Template:** Pilih **Blank** (Kosong).
   - **Space Hardware:** Pilih **CPU basic (Free)**.
   - **Visibility:** Pilih **Public** agar link bisa diakses oleh teman Anda.
3. Klik tombol **"Create Space"**.

---

## Langkah 2: Konfigurasi Metadata (README.md)
Hugging Face membaca konfigurasi container melalui metadata YAML di bagian atas file `README.md`. 
Pastikan file `README.md` di proyek Anda memiliki blok berikut di baris paling atas:

```yaml
---
title: DB-CDM Showcase
emoji: 🚀
colorFrom: blue
colorTo: green
sdk: docker
app_port: 7860
pinned: false
---
```
*(File README.md bawaan di repositori Space Anda akan otomatis memuat blok serupa. Anda bisa mengeditnya secara langsung di tab **"Files"** dashboard Hugging Face Anda).*

---

## Langkah 3: Tambahkan Environment Variables
Agar Laravel berjalan dalam mode produksi dengan kunci aplikasi yang aman, masukkan variabel lingkungan di Hugging Face:
1. Di dashboard Space Anda, buka tab **"Settings"** (di sebelah kanan atas).
2. Gulir ke bawah ke bagian **"Variables and secrets"**.
3. Klik **"New variable"** untuk menambahkan variabel berikut:
   - `APP_ENV` = `production`
   - `APP_DEBUG` = `false`
   - `DB_CONNECTION` = `sqlite`
4. Klik **"New secret"** (karena bersifat rahasia) untuk menambahkan:
   - `APP_KEY` = *(Isi dengan nilai `APP_KEY` dari file `.env` lokal Anda, misalnya `base64:xxxx...`)*

---

## Langkah 4: Upload Kode Anda ke Hugging Face
Anda dapat mengunggah file proyek Anda langsung ke repositori Space menggunakan Git. Di halaman utama Space Anda, cari instruksi Git clone:

```bash
# Clone repositori Space kosong Anda ke komputer lokal
git clone https://huggingface.co/spaces/USERNAME_ANDA/NAMA_SPACE

# Salin semua file proyek DB-CDM Anda ke dalam folder hasil clone tersebut
# (Pastikan file Dockerfile, folder docker, dan folder app Laravel ikut tersalin)

# Lakukan commit dan push ke Hugging Face
git add .
git commit -m "Deploy Laravel App"
git push
```

Hugging Face akan otomatis mendeteksi file `Dockerfile` Anda, mulai membangun kontainer, memicu proses migrasi database SQLite, mengisinya dengan data seeder, dan menjalankan web server Anda di port `7860`.

Setelah statusnya berubah menjadi **Running** (warna hijau), Anda dapat mengakses aplikasi Laravel Anda secara langsung dari link yang disediakan!
