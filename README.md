# SIMASET

SIMASET adalah Sistem Informasi Manajemen Aset berbasis web untuk membantu pengelolaan aset, peminjaman, pengembalian, maintenance, mutasi aset, dan pelaporan.

## Tech Stack
- **Framework**: Laravel 12
- **Language**: PHP 8.3
- **Database**: MySQL 8.0
- **Frontend**: Tailwind CSS, Vite, Node.js 20
- **Web Server**: Nginx

## Requirements
Untuk menjalankan proyek ini, Anda membutuhkan:
- Git
- Docker Desktop (atau Docker Engine & Docker Compose)

Anda **TIDAK** membutuhkan Laragon, XAMPP, PHP, Composer, atau MySQL yang terinstall di komputer Anda karena semua dijalankan di dalam container Docker.

## Installation Using Docker

1. Clone repository ini:
   ```bash
   git clone <repository-url>
   cd simaset
   ```

2. Jalankan Docker Compose:
   ```bash
   docker compose up -d --build
   ```
   > **Note**: Pada saat pertama kali dijalankan, proses ini mungkin membutuhkan waktu karena akan mengunduh image, melakukan instalasi package composer, instalasi NPM, dan proses build Vite.

3. Jika ada proses setup yang terlewat (biasanya sudah otomatis lewat entrypoint), Anda dapat menjalankannya manual:
   ```bash
   docker compose exec app php artisan key:generate
   docker compose exec app php artisan migrate --seed
   ```

## Environment Configuration

Secara default, container akan membuat `.env` dari `.env.docker.example` atau `.env.example`.
Konfigurasi database di Docker:
- `DB_CONNECTION=mysql`
- `DB_HOST=db` (jangan gunakan localhost)
- `DB_PORT=3306`
- `DB_DATABASE=simaset`
- `DB_USERNAME=simaset_user`
- `DB_PASSWORD=secret`

## Database Setup

Docker entrypoint sudah disiapkan untuk menjalankan `php artisan migrate --force` secara otomatis jika container database sudah siap. Jika Anda ingin me-reset database dan menjalankan seeder:
```bash
docker compose exec app php artisan migrate:fresh --seed
```

## Running the Application

Setelah container berjalan, aplikasi dapat diakses di browser melalui URL:
- **Aplikasi**: http://localhost:8000

## Useful Docker Commands

Berikut perintah yang sering digunakan saat pengembangan:
```bash
# Menjalankan container di background
docker compose up -d

# Menghentikan container
docker compose down

# Melihat status container
docker compose ps

# Melihat log dari semua service
docker compose logs -f

# Menjalankan artisan di dalam container app
docker compose exec app php artisan migrate
docker compose exec app php artisan tinker

# Masuk ke shell container app
docker compose exec app bash
```

## Troubleshooting

- **Container gagal berjalan**: Pastikan Docker Desktop berjalan. Coba cek logs menggunakan `docker compose logs app` atau `docker compose logs db`.
- **Port 8000 atau 3306 sudah digunakan**: Jika ada service lokal (seperti Laragon/XAMPP) yang berjalan, matikan terlebih dahulu. Atau ubah mapping port di `compose.yaml` (misal `"8080:80"`).
- **Database connection refused**: Pastikan `DB_HOST` di `.env` bernilai `db`, BUKAN `127.0.0.1` atau `localhost`.
- **Permission storage/cache error**: Masuk ke shell dengan `docker compose exec app bash` dan jalankan `chmod -R 777 storage bootstrap/cache`.
- **Vite asset tidak muncul**: Build asset mungkin terlewat. Jalankan `docker compose exec app npm run build`.
- **Reset semuanya**: Jika terjadi error fatal dan Anda ingin mengulang dari awal:
  ```bash
  docker compose down -v
  docker compose up -d --build
  ```
