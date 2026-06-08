# Panduan Deploy Laravel di AWS EC2 (Free Tier 12 Bulan)

AWS (Amazon Web Services) menyediakan program **Free Tier** di mana Anda mendapatkan VM gratis (EC2 instance `t2.micro` atau `t3.micro` tergantung region) selama **12 bulan**. 

> [!IMPORTANT]
> **Verifikasi Kartu:** AWS mewajibkan pendaftaran kartu kredit/debit untuk verifikasi saat pendaftaran. AWS akan melakukan penahanan dana sementara (sekitar $1 USD / Rp 15.000) yang akan dikembalikan secara otomatis beberapa saat kemudian.

---

## Langkah 1: Buat Instance AWS EC2 (Ubuntu Server)
1. Masuk ke [AWS Console](https://aws.amazon.com/) dan buka layanan **EC2**.
2. Klik tombol **"Launch Instance"**.
3. Konfigurasikan Server:
   - **Name:** `db-cdm-server`
   - **Application and OS Images (AMI):** Pilih **Ubuntu** (Ubuntu Server 24.04 LTS atau 22.04 LTS - *Free Tier Eligible*).
   - **Instance Type:** Pilih `t2.micro` (atau `t3.micro` jika Anda memilih region seperti Singapura yang mendukung t3.micro sebagai free tier).
   - **Key Pair (login):** Klik *Create new key pair*. Beri nama (misal: `db-cdm-key`), pilih format `.pem`, lalu unduh. Simpan file `.pem` ini dengan aman (misal di folder `C:\Users\Username\.ssh\`).
   - **Network Settings (Firewall):** Centang pilihan berikut:
     - *Allow SSH traffic from (Anywhere)*
     - *Allow HTTPS traffic from the internet*
     - *Allow HTTP traffic from the internet*
4. Klik **"Launch Instance"**. Tunggu hingga status instance menjadi *Running*.
5. Catat **Public IPv4 Address** dari instance Anda.

---

## Langkah 2: Hubungkan ke Server via SSH
Buka Terminal (Mac/Linux) atau PowerShell/Command Prompt (Windows), lalu jalankan perintah berikut:
1. Ubah permission file kunci agar aman (hanya perlu di Linux/Mac, di Windows bisa diabaikan jika tidak ada error):
   ```bash
   chmod 400 /path/ke/db-cdm-key.pem
   ```
2. Hubungkan ke server menggunakan IP Publik Anda:
   ```bash
   ssh -i "/path/ke/db-cdm-key.pem" ubuntu@IP_PUBLIK_AWS_ANDA
   ```
   *(Ketik `yes` jika muncul pertanyaan konfirmasi saat pertama kali terhubung).*

---

## Langkah 3: Setup Server Menggunakan Docker (Cara Termudah & Paling Bersih)
Karena kita sudah memiliki `Dockerfile`, cara termudah mendistribusikan aplikasi adalah dengan menggunakan Docker langsung di EC2.

### A. Instal Docker di EC2
Setelah masuk ke dalam SSH server Ubuntu AWS Anda, jalankan perintah ini satu per satu:
```bash
# Update package list
sudo apt-get update

# Install Docker
sudo apt-get install -y docker.io

# Mulai layanan Docker
sudo systemctl start docker
sudo systemctl enable docker

# Berikan hak akses Docker ke user ubuntu agar tidak perlu mengetik 'sudo' terus menerus
sudo usermod -aG docker ubuntu
```
*Catatan: Setelah perintah terakhir, ketik `exit` untuk keluar dari SSH, lalu hubungkan kembali (`ssh -i ...`) agar perubahan hak akses Docker aktif.*

### B. Clone Proyek dari GitHub ke EC2
```bash
# Clone repositori Anda
git clone https://github.com/USERNAME_ANDA/DB-CDM.git

# Masuk ke folder proyek
cd DB-CDM
```

### C. Konfigurasi File Lingkungan (.env) di EC2
Buat file `.env` produksi di dalam folder proyek Anda di EC2:
```bash
nano .env
```
Salin dan tempel konfigurasi berikut (sesuaikan database dengan detail **Supabase** Anda):
```env
APP_NAME=Laravel
APP_ENV=production
APP_DEBUG=false
APP_URL=http://IP_PUBLIK_AWS_ANDA
APP_KEY=base64:Up7uN5TQKk1c+EJqw0vTHRU3QyokoS46MmJPSxhws/o=

DB_CONNECTION=pgsql
DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=password_supabase_anda

LOG_CHANNEL=stderr
```
*Tekan `Ctrl + O` lalu `Enter` untuk menyimpan, dan `Ctrl + X` untuk keluar dari editor nano.*

### D. Build & Jalankan Container Docker Anda
```bash
# Build container menggunakan Dockerfile proyek
docker build -t db-cdm-app .

# Jalankan container di port 80 (HTTP) secara background
docker run -d --name laravel-web -p 80:80 db-cdm-app
```

### E. Jalankan Migrasi Database
Jalankan migrasi di dalam container yang sedang berjalan agar tabel masuk ke database Supabase Anda:
```bash
docker exec -it laravel-web php artisan migrate --force
```

Aplikasi Anda kini sudah aktif dan dapat diakses langsung menggunakan **IP Publik AWS Anda** di web browser!

---

## Langkah 4: Hubungkan Domain & SSL (HTTPS) Gratis (Opsional)
Jika Anda memiliki domain (seperti `proyekku.com`), Anda bisa mengarahkannya ke IP Publik AWS tersebut melalui pengaturan DNS (A Record).
Untuk menambahkan HTTPS gratis, Anda bisa menggunakan **Certbot (Let's Encrypt)** dengan menginstalnya di Ubuntu AWS Anda untuk mengamankan trafik web Anda.
