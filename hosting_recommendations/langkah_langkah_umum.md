# Langkah-Langkah Umum Persiapan Sebelum Deployment

Sebelum Anda mendeploy proyek Laravel ke server hosting manapun (Railway, Render, VPS, dll.), pastikan Anda telah menyelesaikan daftar periksa berikut agar tidak terjadi error 500 saat aplikasi diakses oleh teman Anda.

## Checklist 1: File `.gitignore` & Keamanan
- Pastikan file `.env` **TIDAK** ikut ter-push ke GitHub. Periksa kembali file `.gitignore` di root folder proyek Anda.
- Isi `.gitignore` standar minimal harus menyertakan:
  ```
  .env
  /vendor
  /node_modules
  /public/storage
  /storage/*.key
  ```

## Checklist 2: Generate APP_KEY
- Aplikasi Laravel membutuhkan kunci enkripsi yang unik di server produksi.
- Pastikan Anda menambahkan variabel `APP_KEY` di menu konfigurasi/environment variables pada hosting Anda dengan nilai yang diambil dari file `.env` lokal Anda (misalnya: `base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxx`).

## Checklist 3: Kompilasi Aset (Vite / Mix)
- Jika aplikasi Anda menggunakan TailwindCSS atau JavaScript kustom, aset tersebut harus dikompilasi untuk mode produksi.
- Sebelum push ke GitHub, jalankan perintah berikut di komputer lokal Anda:
  ```bash
  npm run build
  ```
- Ini akan menghasilkan aset yang terkompilasi di dalam folder `public/build`. Pastikan folder `public/build` ikut ter-push ke GitHub agar tampilan website (CSS & JS) termuat dengan sempurna di hosting.

## Checklist 4: Folder Writable (Khusus VPS manual)
- Jika mendeploy di VPS manual, pastikan folder penyimpanan memiliki permission yang benar agar Laravel dapat menulis log, session, dan cache:
  ```bash
  chmod -R 775 storage bootstrap/cache
  chown -R www-data:www-data storage bootstrap/cache
  ```
- *Catatan: Jika menggunakan Railway atau Render, hal ini sudah ditangani otomatis.*

## Checklist 5: Jalankan Database Migration di Server
- Setelah setup database selesai di hosting, pastikan tabel-tabel database dibuat di server produksi.
- Jalankan perintah migrasi dengan bendera `--force` agar berjalan di environment production:
  ```bash
  php artisan migrate --force
  ```
- Jika proyek Anda memiliki data awal (seperti admin default atau kategori), jalankan juga seeder:
  ```bash
  php artisan db:seed --force
  ```
