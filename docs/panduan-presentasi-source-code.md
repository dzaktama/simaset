---
title: "Panduan Presentasi Source Code SIMASET"
---

# Panduan Presentasi Source Code SIMASET

Dokumen ini dipakai sebagai pegangan saat menjelaskan kode di VS Code. Setiap fitur (FR-01 sampai FR-08) punya urutan file yang dibuka, bagian yang ditunjuk, dan kalimat penjelasnya. Path ditulis dari akar proyek (`simaset/`). Nomor baris sesuai kode pada branch `main` saat dokumen ini dibuat, jadi jika file berubah, cari nama fungsinya.

## 1. Gambaran Umum Alur Kode

Semua fitur mengikuti alur MVC Laravel yang sama:

**Route** (`routes/web.php`) → **Middleware / Gate** (hak akses) → **Controller** (logika) → **Model** (`app/Models`) → **Tabel database** (`database/migrations`) → **View Blade** (`resources/views`).

Cara menjelaskan: buka `routes/web.php`, tunjuk route fitur, lalu loncat ke method controllernya (Ctrl+klik pada nama controller di VS Code).

Tiga pola yang berulang di hampir semua fitur dan perlu disebut sekali di awal:

| Pola | Artinya | Contoh file |
|---|---|---|
| `DB::beginTransaction()` ... `commit()` / `rollBack()` | Beberapa perubahan data dianggap satu kesatuan. Jika satu gagal, semua dibatalkan. | `app/Http/Controllers/BorrowingController.php` |
| `AssetHistory::create(...)` | Setiap aksi penting dicatat ke riwayat aset (jejak audit). | `app/Models/AssetHistory.php` |
| Hak akses lewat `can:` | Pengecekan izin per aksi, bukan hanya per peran. | `app/Providers/AppServiceProvider.php` |

## 2. Peta Fitur ke File

| FR | Fitur | Penanggung jawab | File utama yang dibuka (berurutan) |
|---|---|---|---|
| FR-01 | Login | Dinara | `routes/web.php` → `app/Http/Controllers/AuthController.php` → `app/Models/User.php` → `app/Providers/AppServiceProvider.php` |
| FR-02 | CRUD data aset | Dinara | `routes/web.php` → `app/Http/Controllers/AssetController.php` → `app/Models/Asset.php` → `app/Models/AssetHistory.php` |
| FR-03 | Pengajuan peminjaman | Dinara | `routes/web.php` → `app/Http/Controllers/BorrowingController.php` (`store`) → `app/Models/AssetRequest.php` |
| FR-04 | Approval dan monitoring peminjaman | Dinara | `app/Http/Controllers/BorrowingController.php` (`index`, `approve`, `reject`) |
| FR-05 | Pengembalian dan kondisi aset | Dzaky | `app/Http/Controllers/AssetReturnController.php` (`store`, `verify`) → `app/Models/AssetReturn.php` |
| FR-06 | Laporan kerusakan | Dzaky | `app/Http/Controllers/AssetReturnController.php` (`verify`) → `app/Http/Controllers/MaintenanceController.php` (`store`) |
| FR-07 | Teknisi memantau perbaikan | Dzaky | `app/Http/Controllers/MaintenanceController.php` (`index`, `update`) → `app/Models/Maintenance.php` |
| FR-08 | Laporan rekapitulasi | Dzaky | `app/Http/Controllers/ReportController.php` (`index`, `exportPdf`, `exportExcel`) → `app/Services/SimpleXlsx.php` |

Catatan: bagian Dzaky adalah FR-05 sampai FR-08. Pembagian dua orang mengikuti `Pembagian Jobdesk Tekdok`.

## 3. Panduan Per Fitur

### FR-01 Login dan Hak Akses

| Urutan | File dan lokasi | Yang ditunjuk | Kalimat penjelasan |
|---|---|---|---|
| 1 | `routes/web.php` baris 26 | `Route::post('/login', 'login')->middleware('guest')` | Route login hanya bisa diakses tamu. Orang yang sudah login diarahkan keluar oleh middleware `guest`. |
| 2 | `app/Http/Controllers/AuthController.php` baris 27-36 | `$request->validate([... 'captcha' => 'required|captcha'])` | Sebelum cek password, input divalidasi. Captcha mencegah percobaan login otomatis. |
| 3 | `app/Http/Controllers/AuthController.php` baris 44-46 | `Auth::attempt(...)` dan `session()->regenerate()` | `Auth::attempt` mencocokkan email dengan hash bcrypt di database. Jika cocok, ID sesi diganti agar tidak bisa dibajak (session fixation). |
| 4 | `app/Http/Controllers/AuthController.php` baris 53 | `back()->with('loginError', ...)` | Jika gagal, pesan error ditampilkan dan email tetap terisi supaya pengguna tidak mengetik ulang. |
| 5 | `app/Models/User.php` baris 77 | `hasPermission()` | Inti hak akses. Urutannya: izin khusus milik user, lalu bypass Super Admin, lalu izin bawaan peran. |
| 6 | `app/Providers/AppServiceProvider.php` baris 39-47 | `Cache::remember('app_permissions', ...)` dan `Gate::define` | Daftar izin dibaca dari tabel `permissions`, disimpan di cache 24 jam, lalu tiap izin didaftarkan sebagai Gate. Inilah yang membuat `can:borrow.action` di route dan controller bekerja. |

Pertanyaan yang mungkin muncul: "Kenapa password bisa ditolak saat tambah user?" Jawab: `AppServiceProvider.php` baris 29-33 memakai `Password::defaults()` (minimal 8 karakter, huruf besar dan kecil, angka, simbol, dan dicek ke daftar password bocor).

### FR-02 CRUD Data Aset

| Urutan | File dan lokasi | Yang ditunjuk | Kalimat penjelasan |
|---|---|---|---|
| 1 | `routes/web.php` baris 87 | `Route::resource('assets', AssetController::class)` | Satu baris ini membuat route index, create, store, show, edit, update, dan destroy sekaligus. |
| 2 | `app/Http/Controllers/AssetController.php` baris 29-31 | `middleware('can:asset.create')`, `asset.edit`, `asset.delete` | Tiap aksi dijaga izin yang berbeda. Staf biasa bisa melihat, tetapi tidak bisa menambah atau menghapus. |
| 3 | `app/Http/Controllers/AssetController.php` baris 352-364 | `$request->validate([...])` | Semua input divalidasi di server: nama wajib, jumlah minimal 1, foto maksimal 2 MB. |
| 4 | `app/Http/Controllers/AssetController.php` baris 366-382 | Pembuatan prefix dan nomor seri | Nomor seri dibentuk otomatis dari 3 huruf nama aset dan nomor urut 5 digit (contoh `KEY-00001`). Ini menyelesaikan masalah duplikasi nomor seri saat masih memakai spreadsheet. |
| 5 | `app/Http/Controllers/AssetController.php` baris 412-436 | `DB::beginTransaction()` dan perulangan `Asset::create` | Jika jumlah 10, dibuat 10 record dengan stok masing-masing 1, semuanya dalam satu transaksi. Jika satu gagal, tidak ada yang tersimpan. |
| 6 | `app/Http/Controllers/AssetController.php` baris 426-431 | `AssetHistory::create([... 'action' => 'created'])` | Pembuatan aset dicatat ke riwayat. |
| 7 | `app/Models/Asset.php` baris 121-126 | `activeRequest()` | Relasi untuk mengetahui siapa yang sedang memegang aset (peminjaman berstatus approved). |

### FR-03 Pengajuan Peminjaman oleh Staf

| Urutan | File dan lokasi | Yang ditunjuk | Kalimat penjelasan |
|---|---|---|---|
| 1 | `routes/web.php` baris 74 | `Route::post('/borrowing', ...)` | Route pengajuan dari modal "Pinjam" di katalog aset. |
| 2 | `app/Http/Controllers/BorrowingController.php` baris 19-26 | Konstruktor dengan `can:borrow.request` | Hanya yang punya izin `borrow.request` yang boleh mengajukan. |
| 3 | `app/Http/Controllers/BorrowingController.php` baris 104-111 | `$request->validate` | Tanggal kembali tidak boleh sebelum hari ini, alasan wajib diisi. |
| 4 | `app/Http/Controllers/BorrowingController.php` baris 118-119 | `Asset::lockForUpdate()->findOrFail(...)` | Baris aset dikunci selama transaksi, sehingga dua orang yang mengajukan bersamaan tidak bisa mengambil stok yang sama (mencegah double booking). |
| 5 | `app/Http/Controllers/BorrowingController.php` baris 122-131 | Cek `status !== 'available'` dan `quantity <` | Aset rusak, sedang diperbaiki, atau stoknya kurang ditolak sebelum pengajuan dibuat. |
| 6 | `app/Http/Controllers/BorrowingController.php` baris 139-147 | `AssetRequest::create([... 'status' => 'pending'])` | Pengajuan disimpan dengan status `pending`. Stok belum berkurang, baru berkurang saat admin menyetujui. |

### FR-04 Approval dan Monitoring Peminjaman oleh Admin

| Urutan | File dan lokasi | Yang ditunjuk | Kalimat penjelasan |
|---|---|---|---|
| 1 | `routes/web.php` baris 100-104 | Route `/borrowing`, `approve`, `reject` dengan `can:borrow.action` | Aksi ini hanya untuk admin. |
| 2 | `app/Http/Controllers/BorrowingController.php` baris 33 | `index()` | Halaman monitoring: daftar semua pengajuan dengan pencarian dan filter status. |
| 3 | `app/Http/Controllers/BorrowingController.php` baris 285 | `AssetRequest::...->lockForUpdate()->findOrFail($id)` | Pengajuan dikunci supaya tidak disetujui dua kali oleh dua admin. |
| 4 | `app/Http/Controllers/BorrowingController.php` baris 287-294 | Cek `status !== 'pending'` dan cek stok ulang | Stok dicek lagi karena bisa berubah sejak pengajuan dibuat. |
| 5 | `app/Http/Controllers/BorrowingController.php` baris 297-302 | `decrement('quantity', ...)` dan status `deployed` | Stok dikurangi saat disetujui. Jika stok jadi 0, status aset menjadi `deployed`. |
| 6 | `app/Http/Controllers/BorrowingController.php` baris 331 | `reject()` | Penolakan menyimpan catatan admin tanpa mengubah stok. |

### FR-05 Pengembalian Barang dan Pencatatan Kondisi Aset

| Urutan | File dan lokasi | Yang ditunjuk | Kalimat penjelasan |
|---|---|---|---|
| 1 | `routes/web.php` baris 107-108 | `Route::resource('returns', ...)` dan `returns.verify` | Pengembalian dua tahap: staf mengajukan, admin memverifikasi. |
| 2 | `app/Http/Controllers/AssetReturnController.php` baris 14-20 | Middleware `can:return.verify` | Verifikasi hanya untuk admin atau teknisi. Pengajuan (`store`) terbuka untuk pengguna yang login. |
| 3 | `app/Http/Controllers/AssetReturnController.php` baris 44-57 | Validasi `condition` dan `photo_proof_1` | Staf wajib memilih kondisi dan mengunggah minimal satu foto bukti (maksimal tiga). |
| 4 | `app/Http/Controllers/AssetReturnController.php` baris 59-61 | Cek `returned` / `pending_return` | Mencegah pengembalian ganda. |
| 5 | `app/Http/Controllers/AssetReturnController.php` baris 75-89 | `AssetReturn::create([... 'status' => 'pending'])` lalu status request `pending_return` | Data pengembalian dan foto disimpan, status peminjaman menunggu verifikasi. Stok belum bertambah. |
| 6 | `app/Http/Controllers/AssetReturnController.php` baris 97-125 | `verify()`: kondisi akhir dan `increment('quantity', ...)` | Admin boleh mengubah kondisi akhir setelah memeriksa barang. Stok bertambah di sini, dan status aset mengikuti kondisi akhir. |
| 7 | `app/Http/Controllers/AssetReturnController.php` baris 149-165 | Tutup tiket dan `AssetHistory::create(... 'returned')` | Peminjaman ditutup (`returned`), denda dan kondisi akhir masuk riwayat. |

### FR-06 Laporan Kerusakan

Laporan kerusakan lahir dari dua jalur. Jelaskan keduanya.

| Urutan | File dan lokasi | Yang ditunjuk | Kalimat penjelasan |
|---|---|---|---|
| 1 | `app/Http/Controllers/AssetReturnController.php` baris 128-140 | Blok `Maintenance::create([...'status' => 'pending'])` | Jalur otomatis: jika kondisi akhir `broken` atau `maintenance`, sistem langsung membuat tiket perbaikan dengan catatan dari staf. |
| 2 | `app/Http/Controllers/MaintenanceController.php` baris 137-142 | `store()`: cek `Gate::denies('asset.edit')` | Jalur manual: tiket dibuat langsung dari halaman aset, hanya untuk yang punya izin. |
| 3 | `app/Http/Controllers/MaintenanceController.php` baris 143-153 | Validasi | Tanggal mulai tidak boleh sebelum hari ini, deskripsi masalah wajib. |
| 4 | `app/Http/Controllers/MaintenanceController.php` baris 166-168 | Cek `status === 'deployed'` | Aset yang sedang dipinjam tidak boleh diperbaiki dulu, harus dikembalikan. |
| 5 | `app/Http/Controllers/MaintenanceController.php` baris 170-188 | `AssetHistory::create`, `Maintenance::create`, `$asset->update(['status' => 'maintenance'])` | Riwayat dicatat, tiket dibuat berstatus `on_process`, dan aset otomatis tidak bisa dipinjam. |

### FR-07 Teknisi Memantau Perbaikan

| Urutan | File dan lokasi | Yang ditunjuk | Kalimat penjelasan |
|---|---|---|---|
| 1 | `routes/web.php` baris 88 | `Route::resource('maintenances', ...)` | Route modul perbaikan. |
| 2 | `app/Http/Controllers/MaintenanceController.php` baris 12-16 dan 20 | Konstruktor `can:maintenance.view` dan `index()` | Teknisi melihat daftar tiket perbaikan, diurutkan dari tanggal mulai terbaru. |
| 3 | `app/Http/Controllers/MaintenanceController.php` baris 205-209 | `Gate::none(['asset.edit', 'maintenance.action'])` | Teknisi memakai izin `maintenance.action`, admin memakai `asset.edit`. Keduanya boleh mengubah status. |
| 4 | `app/Http/Controllers/MaintenanceController.php` baris 210-218 | Validasi `status` `on_process,completed,cancelled` | Tiga status yang boleh dipilih teknisi. |
| 5 | `app/Http/Controllers/MaintenanceController.php` baris 222-238 | Blok `if ($request->status == 'completed')` | Saat selesai, tanggal selesai dan ID teknisi (`resolver_id`) diisi otomatis, jadi siapa yang menyelesaikan perbaikan tercatat. |
| 6 | `app/Http/Controllers/MaintenanceController.php` baris 254-256 | `status === 'completed'` lalu `asset->status = 'available'` | Perbaikan selesai membuat aset bisa dipinjam lagi. |
| 7 | `app/Http/Controllers/MaintenanceController.php` baris 288-290 | `elseif ($request->status === 'cancelled')` | Jika perbaikan dibatalkan, aset ditandai `broken`. |

### FR-08 Laporan Rekapitulasi Data Aset

| Urutan | File dan lokasi | Yang ditunjuk | Kalimat penjelasan |
|---|---|---|---|
| 1 | `routes/web.php` baris 115-119 | Grup `can:report.view` | Seluruh route laporan dibatasi izin `report.view`. |
| 2 | `app/Http/Controllers/ReportController.php` baris 46-65 | `index()`: `selectRaw('status, COUNT(*)')->groupBy('status')` | Ringkasan jumlah aset per status (tersedia, dipinjam, dll.) diambil dengan satu query agregat, bukan beberapa query. |
| 3 | `app/Http/Controllers/ReportController.php` baris 97 | `exportPdf()` | Menerima filter (pencarian, status, urutan, orientasi, judul, catatan) lalu membuat PDF dengan logo, tabel aset, dan opsi foto. |
| 4 | `app/Http/Controllers/ReportController.php` baris 292-313 | `exportExcel()`: pilihan `csv` atau `xlsx` | Pengguna memilih format. CSV dialirkan langsung (`response()->stream`). |
| 5 | `app/Http/Controllers/ReportController.php` baris 319-330 | Cabang `type === 'borrowing'` | Laporan peminjaman memakai query berbeda dengan filter rentang tanggal. |
| 6 | `app/Services/SimpleXlsx.php` | Kelas pembuat XLSX | File XLSX dibuat oleh service buatan sendiri, tanpa pustaka tambahan. |

## 4. Urutan Saran Saat Presentasi

1. Buka `routes/web.php` dan jelaskan tiga grup route: tamu, login, dan izin dinamis.
2. FR-01: tunjukkan bahwa semua izin berpusat di `User.php` baris 77 dan `AppServiceProvider.php`.
3. FR-02 sampai FR-04: ikuti satu aset dari dibuat, diajukan, sampai disetujui. Stok berubah di `approve()`.
4. FR-05 sampai FR-07: lanjutkan satu aset yang sama dikembalikan rusak, otomatis jadi tiket, lalu diperbaiki teknisi sampai `available` lagi.
5. FR-08: tutup dengan laporan rekap yang merangkum semua status tadi.
6. Buka `database/migrations` bila ditanya struktur tabel: `asset_requests`, `asset_returns`, `maintenances`, `asset_histories`.

Alur satu aset ini saling terhubung lewat kolom `status` pada tabel `assets`:

| Status aset | Diubah oleh | File |
|---|---|---|
| `available` | Aset dibuat atau perbaikan selesai | `AssetController.php`, `MaintenanceController.php` |
| `deployed` | Peminjaman disetujui dan stok habis | `BorrowingController.php` baris 297-302 |
| `broken` / `maintenance` | Verifikasi pengembalian atau tiket perbaikan dibuat | `AssetReturnController.php`, `MaintenanceController.php` |
| `available` lagi | Perbaikan `completed` | `MaintenanceController.php` baris 254-256 |

## 5. Pertanyaan yang Mungkin Ditanyakan

| Pertanyaan | Jawaban singkat | Tunjukkan di |
|---|---|---|
| Kenapa memakai transaksi database? | Supaya stok, status, dan riwayat berubah bersamaan. Jika ada error di tengah, semuanya dibatalkan. | `BorrowingController.php` baris 115 dan 163-169 |
| Bagaimana mencegah dua orang meminjam barang terakhir? | `lockForUpdate()` mengunci baris aset selama transaksi. | `BorrowingController.php` baris 119 |
| Apa bedanya peran dan izin? | Peran punya kumpulan izin bawaan. Pengguna bisa diberi izin khusus yang menimpa peran. | `User.php` baris 77-96 |
| Kenapa stok bertambah saat verifikasi, bukan saat pengajuan pengembalian? | Barang baru dianggap kembali setelah admin memeriksa fisik dan fotonya. | `AssetReturnController.php` baris 97-125 |
| Siapa yang tahu aset ini pernah diubah siapa? | Tabel `asset_histories` mencatat aksi, pelaku, dan catatan. | `app/Models/AssetHistory.php` |
| Apakah kode ini aman dari input jahat? | Semua input divalidasi di server, akses dijaga Gate, dan form dilindungi token CSRF bawaan Laravel. | Metode `validate` di tiap controller |
