# Rekomendasi Hosting untuk Aplikasi Laravel (DB-CDM)

Untuk menampilkan (showcase) aplikasi Laravel ke teman-teman Anda, ada beberapa pilihan hosting terbaik yang mudah digunakan, cepat, dan beberapa di antaranya menyediakan opsi gratis atau sangat murah. Berikut adalah rekomendasinya:

## 1. Railway (Sangat Direkomendasikan ⭐⭐⭐⭐⭐)
Railway adalah platform cloud modern yang sangat mudah digunakan untuk mendeploy aplikasi Laravel secara otomatis langsung dari repository GitHub.
- **Kelebihan:**
  - Integrasi GitHub yang sangat mulus (setiap kali Anda push code, web akan terupdate otomatis).
  - Setup database (MySQL/PostgreSQL) sangat mudah dan cepat dalam satu dashboard.
  - Secara otomatis mendeteksi Laravel menggunakan Nixpacks (tidak perlu ribet konfigurasi server).
  - Skema pembayaran berbasis penggunaan (*Pay-as-you-go*) dengan free credits awal yang cukup untuk showcase.
- **Kekurangan:**
  - Memerlukan verifikasi kartu debit/kredit untuk mendeteksi penyalahgunaan (walau tidak langsung dicharge jika masih dalam limit).

## 2. Render (Alternatif Bagus ⭐⭐⭐⭐)
Render mirip dengan Heroku lama dan memiliki opsi gratis yang sangat ramah pemula.
- **Kelebihan:**
  - Memiliki **Free Tier** untuk Web Services dan PostgreSQL database (berlaku 90 hari untuk database gratis).
  - Integrasi otomatis dengan GitHub.
  - Setup SSL gratis secara otomatis.
- **Kekurangan:**
  - Memerlukan registrasi kartu kredit/debit untuk verifikasi saat ingin membuat database PostgreSQL gratis.
  - Pada opsi gratis, aplikasi akan masuk ke mode "tidur" jika tidak ada aktivitas selama 15 menit. Saat pertama kali diakses kembali, butuh waktu sekitar 30 detik untuk *spin up* (bangun).

## 3. AWS EC2 - Amazon Web Services (Free Tier 12 Bulan ⭐⭐⭐⭐⭐)
Layanan cloud provider terkemuka di dunia. Menyediakan VPS (Virtual Private Server) gratis selama 12 bulan untuk pengguna baru.
- **Kelebihan:**
  - **Gratis selama 12 bulan pertama** untuk jenis server mikro (`t2.micro` / `t3.micro`).
  - Kontrol penuh atas server (bisa menggunakan Docker yang sudah kita siapkan).
  - Sangat stabil, handal, dan standar industri profesional.
- **Kekurangan:**
  - Memerlukan verifikasi kartu kredit/debit saat mendaftar.
  - Setelah 12 bulan berakhir, akan dikenakan biaya sesuai tarif penggunaan normal jika tidak dimatikan.
  - Setup server bersifat manual melalui SSH terminal (namun sudah kami buatkan panduan praktis menggunakan Docker).

## 4. Hostinger / Niagahoster VPS / DomaiNesia (VPS Lokal Murah ⭐⭐⭐⭐)
Jika ingin menggunakan hosting lokal Indonesia agar pembayaran lebih mudah (pake QRIS/E-Wallet/Transfer Bank lokal).
- **Kelebihan:**
  - Lokasi server bisa di Jakarta (akses lebih cepat dari Indonesia).
  - Pembayaran mudah menggunakan Rupiah dan metode pembayaran lokal.
  - Kontrol penuh atas server.
- **Kekurangan:**
  - Setup bersifat manual. Lebih rumit untuk pemula dibanding Cloud PaaS seperti Railway/Render.

## 5. Zeabur (Sangat Direkomendasikan Tanpa Kartu Kredit ⭐⭐⭐⭐⭐)
Platform PaaS modern seperti Railway yang mendukung deployment otomatis dari GitHub tanpa perlu kartu kredit.
- **Kelebihan:**
  - **Sama sekali tidak memerlukan kartu kredit** untuk menggunakan paket gratisnya.
  - Sangat mudah digunakan, mendukung deployment otomatis dari GitHub.
- **Kekurangan:**
  - Paket gratis memiliki batas resource (credits) bulanan.

---

> [!TIP]
> **Pilihan Terbaik untuk Belajar Cloud Profesional:** Gunakan **AWS EC2 (Free Tier)**. Dengan menggunakan AWS, Anda belajar mengelola VPS sendiri secara nyata (standar industri) secara gratis selama setahun penuh.
