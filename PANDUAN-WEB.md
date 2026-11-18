# Panduan Menyambungkan Web Pasolex ke Database (Laravel)

Web ini hanya untuk **Staf Admin** dan **Pemilik**. Staf gudang akan memakai aplikasi tersendiri (tahap berikutnya)
dan, kalau mencoba login di web ini, akan diberi pesan yang menunjukkan hal itu.

> Status pengujian: tampilan web sudah diuji di browser sungguhan terhadap server tiruan yang meniru logika
> controller ini. File PHP-nya sendiri **belum pernah dijalankan** oleh pembuatnya (tidak ada PHP di lingkungan
> pembuat). Jadi kalau ada error saat pertama dijalankan, salin pesannya apa adanya dan kirim supaya diperbaiki.

## Yang dibutuhkan
1. Database `pasolex_store` sudah diimpor lewat phpMyAdmin (file `pasolex_store.sql`). **Jangan** jalankan `php artisan migrate`.
2. PHP 8.2 atau lebih baru dan Composer. Cek di terminal: `php -v` dan `composer -V`.
3. MySQL XAMPP Anda berjalan di **port 3307** (terlihat di phpMyAdmin: "Server: localhost 3307").

## Langkah
**1. Buat proyek Laravel kosong** (terminal, di folder tempat Anda menyimpan proyek):
```
composer create-project laravel/laravel pasolex-web
cd pasolex-web
```

**2. Atur koneksi database.** Buka file `.env` di folder `pasolex-web`, cari dan ubah baris-baris ini:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=pasolex_store
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
CACHE_STORE=file
```
Kalau di `.env` ada `CACHE_DRIVER=` (Laravel lama), ganti menjadi `CACHE_DRIVER=file`.
Alasan `SESSION_DRIVER=file`: Laravel baru menyimpan sesi login di tabel `sessions`, padahal tabel itu tidak ada
di database kita. Dengan `file`, sesi disimpan di folder `storage` sehingga tidak butuh tabel.

**3. Salin file paket ini** ke dalam folder `pasolex-web`, dengan struktur yang sama:
```
routes/web.php                            -> TIMPA file bawaan
app/Http/Controllers/AuthController.php   -> baru
app/Http/Controllers/DataController.php   -> baru
app/Http/Controllers/MasterController.php -> baru
app/Http/Middleware/WebAuth.php           -> baru (buat folder Middleware kalau belum ada)
resources/views/app.blade.php             -> baru (seluruh tampilan web)
```

**4. Jalankan:**
```
php artisan config:clear
php artisan serve
```
Buka **http://127.0.0.1:8000**. Login dengan `admin` / `123456` atau `pemilik` / `123456`.
(Setiap kali `.env` diubah: hentikan server dengan Ctrl+C, jalankan `php artisan config:clear`, lalu `php artisan serve` lagi.)

## Cara kerjanya (alur satu permintaan)
```
Browser (app.blade.php)  --fetch-->  routes/web.php  -->  Controller  -->  MySQL (pasolex_store)
```
| Bagian | Fungsi |
|---|---|
| `routes/web.php` | Daftar alamat. Contoh: `POST /login` diarahkan ke `AuthController::login` |
| `WebAuth` (middleware) | Penjaga pintu. Memeriksa sudah login atau belum, dan peran admin/pemilik. Pemilik yang mencoba menulis data ditolak dengan kode 403 |
| `AuthController` | Login (memeriksa password ter-hash bcrypt), logout, dan cek sesi |
| `DataController` | Mengirim semua data yang dibutuhkan dashboard (produk, stok, mutasi, opname, akun) |
| `MasterController` | Tambah/ubah/hapus produk, warna, rak, jenis kerusakan, tindak lanjut, dan akun, lengkap dengan validasi |
| `app.blade.php` | Tampilan Pasolex (HTML, CSS, JavaScript). Data diambil dari `/api/data` setiap pindah menu |

## Yang berbeda dari versi demo sebelumnya
- Data tersimpan di MySQL, bukan di browser. Data awal dari `pasolex_store.sql`.
- Tombol Stok Masuk, Stok Keluar, Barang Rusak, Hitung Fisik, dan Cari Lokasi Rak **dihapus** dari web
  (pindah ke aplikasi staf gudang). Di web, Inventory hanya untuk memantau: Daftar Stok, riwayat Barang Rusak, dan riwayat Stock Opname.
- Tab Akun di Master Data bisa membuat akun untuk ketiga peran, termasuk staf gudang. Username dicek unik di ketiga tabel.
- Laporan dihitung di browser dari data yang sama. Rentang tanggal awal otomatis dimulai dari mutasi paling awal.
  Tombol PDF memakai fungsi cetak browser, tombol Excel mengunduh file CSV.

## Kalau muncul error
| Pesan | Penyebab dan solusi |
|---|---|
| `could not find driver` | Ekstensi MySQL PHP belum aktif. Di XAMPP, buka `php.ini` dan hapus tanda `;` pada `extension=pdo_mysql` |
| `SQLSTATE[HY000] [2002] Connection refused` | MySQL belum berjalan, atau `DB_PORT` salah (harus 3307) |
| `SQLSTATE[HY000] [1045] Access denied` | `DB_USERNAME` atau `DB_PASSWORD` di `.env` salah |
| `Unknown database 'pasolex_store'` | Database belum diimpor, atau `DB_DATABASE` salah ketik |
| `Table 'pasolex_store.sessions' doesn't exist` | `SESSION_DRIVER=file` belum diatur, atau lupa `php artisan config:clear` |
| Halaman berisi "Page Expired" / kode 419 | Muat ulang halaman (Ctrl+F5) lalu coba lagi |
| Layar putih atau kode 500 | Buka `storage/logs/laravel.log`, lihat baris error paling bawah, lalu kirimkan |
| Login selalu gagal | Pastikan database hasil impor dari `pasolex_store.sql` versi terbaru, bukan data lama |

## Langkah berikutnya
Aplikasi staf gudang (login, inbound, outbound, barang rusak, stock opname, cari lokasi rak) yang memakai database yang sama.
