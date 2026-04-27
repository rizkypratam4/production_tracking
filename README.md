# Production Tracking System

Sistem manajemen aset dan penjadwalan produksi berbasis web untuk industri manufaktur kasur. Dibangun dengan Laravel 12, aplikasi ini mencakup pelacakan aset, manajemen maintenance, penjadwalan produksi (Finish Good & WIP), manajemen operator, serta pelaporan produksi.

---

## Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Tech Stack](#tech-stack)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Database](#database)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Akun Default](#akun-default)
- [Struktur Modul](#struktur-modul)
- [Struktur Database](#struktur-database)
- [Docker](#docker)

---

## Fitur Utama

- **Dashboard** — Ringkasan produksi harian, mingguan, dan tahunan dengan tren Finish Good & WIP
- **Manajemen Aset** — CRUD aset lengkap dengan spesifikasi mesin, mutasi aset, dan QR code
- **Penjadwalan Produksi** — Import/export jadwal Finish Good dan WIP via Excel
- **Manajemen Operator** — Pencatatan status produksi operator (selesai/pending)
- **Laporan Produksi** — Export laporan ke Excel
- **Master Data** — Area, Departemen, Divisi, Lokasi, Brand, Kategori, Tipe, Work Place
- **Profil Pengguna** — Upload foto profil, edit info personal dan data kerja
- **QR Code Aset** — Generate dan download QR code aset dalam format PDF

---

## Tech Stack

| Komponen | Teknologi |
|---|---|
| Backend Framework | Laravel 12 |
| PHP | ^8.2 |
| Frontend | Tailwind CSS v4, Vite v6 |
| Database | SQLite (default) / MySQL / PostgreSQL |
| PDF | barryvdh/laravel-dompdf |
| Excel | maatwebsite/excel + phpoffice/phpspreadsheet |
| QR Code | simplesoftwareio/simple-qrcode |
| Query Builder | spatie/laravel-query-builder |
| Testing | Pest PHP |
| Containerization | Docker (php:8.3-fpm-alpine) |

---

## Persyaratan Sistem

- PHP >= 8.2
- Composer
- Node.js >= 18 & NPM
- SQLite / MySQL / PostgreSQL
- Extension PHP: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `gd`

---

## Instalasi

### 1. Clone Repository

```bash
git clone <repository-url>
cd production_tracking
```

### 2. Install Dependensi PHP

```bash
composer install
```

### 3. Install Dependensi Node.js

```bash
npm install
```

### 4. Salin File Environment

```bash
cp .env.example .env
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Buat Storage Symlink

```bash
php artisan storage:link
```

---

## Konfigurasi

Edit file `.env` sesuai kebutuhan:

```env
APP_NAME="Production Tracking"
APP_URL=http://localhost:8000

# Database (default SQLite)
DB_CONNECTION=sqlite
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=production_tracking
# DB_USERNAME=root
# DB_PASSWORD=
```

---

## Database

### Migrasi

```bash
php artisan migrate
```

### Seeding Data Awal

```bash
php artisan db:seed
```

Seeder yang tersedia:

| Seeder | Keterangan |
|---|---|
| `UserSeeder` | 2 akun pengguna default |
| `AreaSeeder` | Data area/wilayah |
| `DepartementSeeder` | Data departemen |
| `DivisionSeeder` | Data divisi |
| `WorkPlaceSeeder` | Data lokasi kerja |
| `BrandSeeder` | Data brand aset |
| `CategorySeeder` | Data kategori aset |
| `TypeSeeder` | Data tipe aset |
| `LocationSeeder` | Data lokasi aset |
| `MaintenanceSeeder` | Data maintenance awal |

---

## Menjalankan Aplikasi

### Development

Jalankan semua service sekaligus:

```bash
composer run dev
```

Atau jalankan secara terpisah:

```bash
# Terminal 1 - Laravel server
php artisan serve

# Terminal 2 - Vite (asset bundler)
npm run dev
```

Akses aplikasi di: `http://localhost:8000`

### Production Build

```bash
npm run build
php artisan optimize
```

---

## Akun Default

Setelah menjalankan seeder, gunakan akun berikut untuk login:

| Nama | Email | Password |
|---|---|---|
| Rizky Pratama | rizkypratama@gmail.com | password |
| Ploren | ploren@gmail.com | password |

> **Penting:** Ganti password setelah login pertama kali.

---

## Struktur Modul

### Master Data

| Modul | URL | Keterangan |
|---|---|---|
| Area | `/areas` | Manajemen area/wilayah |
| Departemen | `/departements` | Manajemen departemen |
| Divisi | `/divisions` | Manajemen divisi per departemen |
| Work Place | `/work_places` | Manajemen lokasi kerja |
| Brand | `/brands` | Brand aset |
| Kategori | `/categories` | Kategori aset |
| Tipe | `/types` | Tipe aset |
| Lokasi | `/locations` | Lokasi penempatan aset |

### Operasional

| Modul | URL | Keterangan |
|---|---|---|
| Dashboard | `/dashboard` | Ringkasan & analitik produksi |
| Maintenance (Aset) | `/maintenances` | CRUD aset, spesifikasi, mutasi, QR code |
| FG Schedule | `/finish_good_schedules` | Jadwal produksi Finish Good |
| WIP Schedule | `/wip_schedules` | Jadwal produksi Work In Progress |
| Operator | `/operators` | Pencatatan operator produksi |
| Laporan Produksi | `/production_reports` | Laporan & export produksi |

### Profil

| Fitur | URL |
|---|---|
| Lihat Profil | `/profiles/{username}` |
| Edit Info Personal | `/profiles/{username}/edit-info` |
| Edit Data Kerja | `/profiles/{username}/edit-work` |
| Ganti Password | `/profiles/{username}/edit-password` |

---

## Struktur Database

```
users
├── areas (area_id)
├── departements (departement_id)
├── divisions (division_id)
└── work_places (work_place_id)

assets
├── brands (brand_id)
├── categories (category_id)
├── types (type_id)
├── work_places (work_place_id)
├── departements (departement_id)
├── machine_specifications (asset_id)
└── asset_mutations (asset_id)
    ├── users (user_id)
    └── locations (location_id)

finish_good_schedules
├── areas (area_id)
├── work_places (work_place_id)
└── operators (finish_good_schedule_id)

wip_schedules
├── areas (area_id)
├── work_places (work_place_id)
└── operators (wip_schedule_id)
```

---

## Docker

Aplikasi mendukung containerization menggunakan Docker dengan base image `php:8.3-fpm-alpine`.

```bash
# Build image
docker build -t production-tracking .

# Jalankan container
docker run -p 9000:9000 production-tracking
```

> Dockerfile saat ini menggunakan PHP-FPM. Untuk deployment lengkap, tambahkan konfigurasi Nginx sebagai web server di depannya.

---

## Lisensi

Project ini menggunakan lisensi [MIT](https://opensource.org/licenses/MIT).
