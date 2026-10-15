# AutoCare

**AutoCare** adalah aplikasi reservasi dan pengelolaan layanan bengkel mobil. Pelanggan dapat mengelola kendaraan dan booking servis, sementara admin mengatur layanan serta memproses reservasi melalui dashboard.

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-10-FF2D20?logo=laravel&logoColor=white" alt="Laravel 10">
  <img src="https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php&logoColor=white" alt="PHP 8.1 or newer">
  <img src="https://img.shields.io/badge/Database-MySQL-4479A1?logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/UI-Blade-FF2D20?logo=laravel&logoColor=white" alt="Blade">
</p>

## Daftar isi

- [Fitur](#fitur)
- [Teknologi](#teknologi)
- [Persiapan](#persiapan)
- [Instalasi](#instalasi)
- [Akun development](#akun-development)
- [Akses aplikasi](#akses-aplikasi)
- [API](#api)

## Fitur

### Pelanggan

- Melihat layanan, testimoni, dan informasi bengkel.
- Mengelola profil serta data kendaraan.
- Membuat dan melihat reservasi servis.
- Melihat riwayat servis dan mengirim testimoni.

### Admin

- Memantau dashboard dan reservasi yang masuk.
- Mengelola layanan, pelanggan, dan kendaraan.
- Memperbarui status booking serta mencatat pekerjaan servis.
- Memoderasi testimoni dan mengelola profil admin.

## Teknologi

- Laravel 10 dan PHP 8.1 atau lebih baru
- MySQL
- Blade dan CSS untuk antarmuka web
- Vite untuk aset frontend
- Laravel Sanctum untuk autentikasi API berbasis token

## Persiapan

Pastikan perangkat sudah memiliki:

- PHP 8.1+ beserta ekstensi yang dibutuhkan Laravel
- Composer
- Node.js dan npm
- MySQL

## Instalasi

Jalankan perintah berikut dari folder proyek:

```bash
composer install
```

Buat file environment. Di macOS/Linux:

```bash
cp .env.example .env
```

Di Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Buat application key, lalu atur koneksi database pada `.env` (misalnya `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD`):

```bash
php artisan key:generate
```

Buat database MySQL sesuai nama pada `.env`, kemudian jalankan migration dan seeder:

```bash
php artisan migrate --seed
```

Pasang dependency frontend:

```bash
npm install
```

Jalankan aplikasi. Biarkan Vite berjalan di satu terminal:

```bash
npm run dev
```

Di terminal lain, jalankan server Laravel:

```bash
php artisan serve
```

Buka alamat yang ditampilkan oleh Artisan, biasanya `http://127.0.0.1:8000`.


## Akses aplikasi

- Halaman publik: `/`
- Login: `/login`
- Registrasi pelanggan: `/register`
- Dashboard pelanggan: `/customer/dashboard`
- Dashboard admin: `/admin/dashboard`

## API

API tersedia di prefix `/api`. Route publik dapat diakses tanpa token; route yang dilindungi menggunakan Laravel Sanctum. Setelah login, kirim token pada header berikut:

```http
Authorization: Bearer <token>
Accept: application/json
```

Endpoint utama:

| Akses | Endpoint |
| --- | --- |
| Publik | `GET /api/services`, `GET /api/services/{service}`, `GET /api/testimonials` |
| Autentikasi | `POST /api/register`, `POST /api/login`, `POST /api/logout` |
| Pelanggan | `/api/customer/dashboard`, `/api/customer/profile`, `/api/customer/vehicles`, `/api/customer/bookings`, `/api/customer/service-history`, `/api/customer/testimonials` |
| Admin | `/api/admin/dashboard`, `/api/admin/bookings`, `/api/admin/services`, `/api/admin/customers`, `/api/admin/vehicles`, `/api/admin/service-records`, `/api/admin/testimonials`, `/api/admin/profile` |

Registrasi API selalu membuat akun dengan peran pelanggan. Endpoint admin dan pelanggan memerlukan autentikasi serta peran yang sesuai.
