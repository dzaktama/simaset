# Penjelasan File `routes/web.php` (Peta Jalan Aplikasi)

Halo! Dokumen ini berisi penjelasan santai tentang isi file `routes/web.php`. 
Bayangkan file ini sebagai **Daftar Menu** atau **Peta Jalan**. Di sini kita ngasih tau aplikasi:
*"Kalau user minta alamat X, tolong arahin ke pelayan (Controller) Y."*

---

## 🚦 1. Pintu Masuk Utama

```php
Route::get('/', function () {
    return redirect()->route('login');
});
```
**Artinya:**
Kalau ada yang buka halamat utama (misal: `simaset.com/`), langsung tendang dia ke halaman Login. Kita gak mau ada tamu nyasar kan?

---

## 🔐 2. Autentikasi (Satpam)

```php
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm'); // Tampilin form login
    Route::post('/login', 'login');        // Proses data login
    Route::post('/logout', 'logout');      // Keluar aplikasi
});
```
**Artinya:**
Ini urusan masuk-keluar aplikasi. Yang boleh akses halaman login cuma tamu (`guest`). Kalau udah login, ya ngapain login lagi?

---

## 🏠 3. Area Dalam (Khusus yang Sudah Login)

Semua route di bawah ini dibungkus pakai `middleware(['auth'])`. Artinya: **"Cuma yang punya KTP (sudah login) yang boleh masuk sini!"**

### A. Fitur Umum (Bisa Diakses Semua Karyawan)

**Dashboard & Statistik**
```php
Route::get('/home', [AssetController::class, 'dashboard']);
// ... route chart lainnya ...
```
Ini halaman pertama yang muncul habis login. Isinya grafik-grafik cantik.

**Katalog & Peta Aset**
```php
Route::get('/assets', ...);      // Liat semua barang
Route::get('/assets/{asset}', ...); // Liat detail 1 barang
Route::get('/assets/map', ...);  // Liat peta lokasi barang
Route::get('/assets/my', ...);   // Liat barang yang SAYA pegang
```
Di sini karyawan bisa liat-liat inventaris kantor, atau ngecek "Gue lagi pinjem apa aja ya?".

**📱 FITUR CHAT (Percakapan)**
```php
Route::get('/chat', ...);              // Buka halaman chat
Route::get('/chat/conversation/...');  // Ambil history chat
Route::post('/chat/send', ...);        // Kirim pesan
```
Jalur komunikasi antar karyawan. Bisa chat personal & kirim info barang.

**Transaksi Peminjaman**
```php
Route::post('/borrowing', ...);      // "Saya mau pinjam ini dong!"
Route::get('/borrowing/history', ...); // Sejarah peminjaman saya
Route::get('/borrowing/{id}', ...);    // Detail peminjaman
Route::post('/.../return-user', ...);  // "Oke, barangnya saya balikin."
```
Ini alur utama kalau karyawan butuh alat kerja baru.

---

### B. Area Operasional (Admin & Teknisi)

```php
Route::middleware(['role:admin,super_admin,service_center'])->group(...)
```
**Artinya:**
Hanya Admin, Super Admin, dan Teknisi Service Center yang boleh masuk sini.
*   **Assets Resource**: Bisa Tambah/Edit Barang (Kecuali Hapus/Destroy).
*   **Maintenances**: Bisa ngurusin tiket perbaikan (Service).

---

### C. Area Terlarang (Admin Only)

```php
Route::middleware(['role:admin,super_admin'])->group(...)
```
**Artinya:**
Teknisi Service Center DILARANG masuk sini. Cuma Admin & Super Admin.
*   **Approval**: Setujui/Tolak peminjaman.
*   **Returns**: Verifikasi pengembalian barang.
*   **Laporan**: Cetak laporan PDF manajemen.

---

### D. Area Dewa (Super Admin Only)

```php
Route::middleware(['role:super_admin'])->group(...)
```
**Artinya:**
Area paling sakral.
*   **Users Management**: Tambah karyawan baru, pecat karyawan, ganti password orang. Bahaya kalau sembarang orang bisa akses ini.

---

## 📝 Kesimpulan
File ini rapi banget dibagi-bagi berdasarkan **"Siapa yang boleh akses"**:
1.  **Publik**: Cuma Login.
2.  **Karyawan Biasa**: Bisa liat aset, chat, dan minjem barang.
3.  **Teknisi**: Bisa nambah data aset & servis barang.
4.  **Admin**: Bisa ACC peminjaman & liat laporan.
5.  **Super Admin**: Bisa segalanya (termasuk ngatur User).

Semoga penjelasannya mencerahkan! 💡
