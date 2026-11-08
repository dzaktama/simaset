---
title: "Panduan Presentasi Source Code SIMASET"
---

# Panduan Presentasi Source Code SIMASET

Dokumen ini dipakai sebagai pegangan saat menjelaskan kode di VS Code. Setiap fitur (FR-01 sampai FR-08) punya urutan file yang dibuka, bagian yang ditunjuk, dan kalimat penjelasnya. Path ditulis dari akar proyek (`simaset/`). Di kode sudah ditambahkan komentar `[PRESENTASI FR-xx #n]`. Cari teks itu (Ctrl+Shift+F di VS Code) untuk langsung loncat ke bagian yang dimaksud. Nomor baris dihitung saat dokumen ini dibuat; jika file berubah, andalkan tag-nya.

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

Cara memakai tabel: buka file di kolom **File dan baris**, cari komentar `[PRESENTASI FR-xx #n]` yang sudah ditambahkan di kode (komentar itu tepat di atas baris yang ditunjuk). Kolom **Jenis** memberi tahu apakah yang ditunjuk itu route, fungsi (method), variabel, kondisi if, pemanggilan fungsi, atau class, supaya istilahnya tepat saat bicara. Kolom **Yang diucapkan** adalah kalimat yang bisa dibaca atau diucapkan ulang dengan kata sendiri.


### FR-01 Login dan Hak Akses


| No | File dan baris | Yang ditunjuk | Jenis | Yang diucapkan |
|---|---|---|---|---|
| 1 | `routes/web.php` baris 28 (tag FR-01 #1) | `Route::post('/login', 'login')->middleware('guest')` | Route (definisi URL) | Ini route, yaitu pintu masuk URL /login dengan method POST. Middleware guest membuat orang yang sudah login tidak bisa membukanya lagi. |
| 2 | `app/Http/Controllers/AuthController.php` baris 32 (tag FR-01 #2) | `$request->validate([...])`, khususnya aturan `'captcha' => 'required|captcha'` | Pemanggilan method validate() | Ini pemanggilan method validate. Sebelum password dicek, email, password, dan captcha divalidasi dulu supaya bot tidak bisa mencoba login berulang. |
| 3 | `app/Http/Controllers/AuthController.php` baris 48 (tag FR-01 #3) | `if (Auth::attempt($credentials, $remember))` | Kondisi if dengan method Auth::attempt() | Ini kondisi if. Method Auth::attempt mencocokkan email dengan password yang di-hash bcrypt di database. Kalau cocok, ID sesi diganti agar tidak bisa dibajak. |
| 4 | `app/Http/Controllers/AuthController.php` baris 59 (tag FR-01 #4) | `return back()->with('loginError', ...)` | Statement return (redirect + pesan) | Ini statement return. Kalau login gagal, pengguna dikembalikan ke form dengan pesan error dan emailnya tetap terisi. |
| 5 | `app/Models/User.php` baris 79 (tag FR-01 #5) | `public function hasPermission($permission)` | Method (fungsi) pada Model | Ini method di model User. Semua pengecekan hak akses berpusat di sini, dengan tiga lapis: izin khusus user, bypass Super Admin, lalu izin bawaan peran. |
| 6 | `app/Providers/AppServiceProvider.php` baris 43 (tag FR-01 #6) | `$permissions = Cache::remember('app_permissions', ...)` | Variabel $permissions dengan Cache::remember() | Ini variabel yang diisi dari cache. Daftar izin diambil dari tabel permissions dan disimpan 24 jam supaya aplikasi tidak bolak-balik ke database. |
| 7 | `app/Providers/AppServiceProvider.php` baris 50 (tag FR-01 #7) | `Gate::define($permission, function ($user) ...)` | Pemanggilan method Gate::define() di dalam foreach | Ini pemanggilan Gate::define di dalam perulangan. Tiap izin didaftarkan sebagai Gate, jadi penulisan can:nama-izin di route dan controller langsung berfungsi. |

### FR-02 CRUD Data Aset


| No | File dan baris | Yang ditunjuk | Jenis | Yang diucapkan |
|---|---|---|---|---|
| 1 | `routes/web.php` baris 93 (tag FR-02 #1) | `Route::resource('assets', AssetController::class)` | Route resource | Ini route resource. Satu baris menghasilkan seluruh route CRUD untuk aset. |
| 2 | `app/Http/Controllers/AssetController.php` baris 31 (tag FR-02 #2) | tiga baris `$this->middleware('can:asset.create' / 'asset.edit' / 'asset.delete')` | Pemanggilan method middleware() di konstruktor | Ini konstruktor controller yang memasang middleware. Setiap aksi punya izin sendiri, jadi staf biasa bisa melihat aset tetapi tidak bisa menambah atau menghapus. |
| 3 | `app/Http/Controllers/AssetController.php` baris 356 (tag FR-02 #3) | `$request->validate([...])` | Pemanggilan method validate() | Ini validasi input. Nama wajib diisi, jumlah minimal 1, dan foto harus gambar maksimal 2 MB. |
| 4 | `app/Http/Controllers/AssetController.php` baris 373 (tag FR-02 #4) | `$prefix = strtoupper(substr(preg_replace(...), 0, 3))` | Variabel $prefix (assignment) | Ini variabel prefix. Isinya tiga huruf pertama nama aset, misalnya Keyboard menjadi KEY, sebagai awalan nomor seri. |
| 5 | `app/Http/Controllers/AssetController.php` baris 381 (tag FR-02 #5) | `$lastAsset = Asset::where('serial_number', 'regexp', ...)` | Variabel $lastAsset (hasil query) | Ini variabel hasil query. Sistem mencari nomor seri terakhir dengan awalan yang sama, sehingga nomor berikutnya tidak pernah kembar. |
| 6 | `app/Http/Controllers/AssetController.php` baris 422 (tag FR-02 #6) | `DB::beginTransaction();` sampai `DB::commit();` | Pemanggilan method transaksi database | Ini awal transaksi database. Kalau saya input 10 unit lalu satu gagal, kesepuluhnya dibatalkan, jadi data tidak setengah tersimpan. |
| 7 | `app/Http/Controllers/AssetController.php` baris 435 (tag FR-02 #7) | `$asset = Asset::create($assetData);` di dalam `for` | Variabel $asset dengan method create() | Ini perulangan yang membuat satu record per unit. Input 10 akan menjadi 10 baris dengan nomor seri berurutan. |
| 8 | `app/Http/Controllers/AssetController.php` baris 440 (tag FR-02 #8) | `AssetHistory::create([... 'action' => 'created'])` | Pemanggilan method create() pada model AssetHistory | Ini pencatatan riwayat. Setiap aset baru masuk ke tabel asset_histories sebagai jejak audit. |
| 9 | `app/Models/Asset.php` baris 123 (tag FR-02 #9) | `public function activeRequest()` | Method relasi pada Model | Ini method relasi di model Asset. Dari sini sistem tahu siapa yang sedang memegang aset, yaitu peminjaman yang berstatus approved. |

### FR-03 Pengajuan Peminjaman oleh Staf


| No | File dan baris | Yang ditunjuk | Jenis | Yang diucapkan |
|---|---|---|---|---|
| 1 | `routes/web.php` baris 78 (tag FR-03 #1) | `Route::post('/borrowing', [BorrowingController::class, 'store'])` | Route (definisi URL) | Ini route pengajuan peminjaman. Dipanggil dari tombol Pinjam di katalog aset. |
| 2 | `app/Http/Controllers/BorrowingController.php` baris 25 (tag FR-03 #2) | `$this->middleware('can:borrow.request')->only(['store'])` | Pemanggilan method middleware() di konstruktor | Ini middleware di konstruktor. Hanya yang punya izin borrow.request yang boleh mengajukan peminjaman. |
| 3 | `app/Http/Controllers/BorrowingController.php` baris 110 (tag FR-03 #3) | `$request->validate([...])` | Pemanggilan method validate() | Ini validasi. Aset harus benar-benar ada, tanggal kembali tidak boleh sebelum hari ini, dan alasan wajib diisi. |
| 4 | `app/Http/Controllers/BorrowingController.php` baris 126 (tag FR-03 #4) | `$asset = Asset::lockForUpdate()->findOrFail(...)` | Variabel $asset dengan method lockForUpdate() | Ini variabel yang diisi dengan query terkunci. lockForUpdate mengunci baris aset, jadi dua orang yang mengajukan bersamaan tidak bisa berebut stok yang sama. |
| 5 | `app/Http/Controllers/BorrowingController.php` baris 131 (tag FR-03 #5) | `if ($asset->status !== 'available')` | Kondisi if (cek status aset) | Ini kondisi if. Aset yang rusak, sedang diperbaiki, atau sudah dipinjam langsung ditolak. |
| 6 | `app/Http/Controllers/BorrowingController.php` baris 150 (tag FR-03 #6) | `AssetRequest::create([... 'status' => 'pending'])` | Pemanggilan method create() pada model AssetRequest | Ini penyimpanan transaksi peminjaman dengan status pending. Stok belum berkurang, baru berkurang saat admin menyetujui. |

### FR-04 Approval dan Monitoring Peminjaman oleh Admin


| No | File dan baris | Yang ditunjuk | Jenis | Yang diucapkan |
|---|---|---|---|---|
| 1 | `routes/web.php` baris 110 (tag FR-04 #1) | `Route::post('/borrowing/{id}/approve', ...)->middleware('can:borrow.action')` | Route (definisi URL) | Ini route persetujuan. Middleware can:borrow.action membuatnya hanya bisa diakses admin. |
| 2 | `app/Http/Controllers/BorrowingController.php` baris 36 (tag FR-04 #2) | `public function index(Request $request)` | Method (fungsi) controller | Ini method index untuk monitoring. Admin melihat semua pengajuan, bisa mencari dan memfilter berdasarkan status. |
| 3 | `app/Http/Controllers/BorrowingController.php` baris 298 (tag FR-04 #3) | `$assetRequest = AssetRequest::with('asset')->lockForUpdate()->findOrFail($id)` | Variabel $assetRequest dengan method lockForUpdate() | Ini variabel pengajuan yang dikunci. Dua admin tidak bisa menyetujui pengajuan yang sama secara bersamaan. |
| 4 | `app/Http/Controllers/BorrowingController.php` baris 302 (tag FR-04 #4) | `if ($assetRequest->status !== 'pending')` | Kondisi if (cek status pengajuan) | Ini kondisi if. Hanya pengajuan pending yang boleh disetujui, dan stok dicek ulang karena bisa berubah sejak diajukan. |
| 5 | `app/Http/Controllers/BorrowingController.php` baris 314 (tag FR-04 #5) | `$assetRequest->asset->decrement('quantity', ...)` | Pemanggilan method decrement() | Ini method decrement yang mengurangi stok di data master aset. Kalau stok jadi nol, status aset menjadi deployed. |
| 6 | `app/Http/Controllers/BorrowingController.php` baris 350 (tag FR-04 #6) | `public function reject(Request $request, $id)` | Method (fungsi) controller | Ini method reject. Admin menolak dengan catatan, status berubah, dan stok tidak berubah. |

### FR-05 Pengembalian Barang dan Pencatatan Kondisi Aset


| No | File dan baris | Yang ditunjuk | Jenis | Yang diucapkan |
|---|---|---|---|---|
| 1 | `routes/web.php` baris 119 (tag FR-05 #1) | `Route::post('/returns/{return}/verify', ...)` | Route (definisi URL) | Ini route verifikasi. Pengembalian punya dua tahap: staf mengajukan, lalu admin memverifikasi. |
| 2 | `app/Http/Controllers/AssetReturnController.php` baris 32 (tag FR-05 #2) | `$this->middleware('can:return.verify')->only([...])` | Pemanggilan method middleware() di konstruktor | Ini middleware di konstruktor. Verifikasi hanya untuk admin atau teknisi, sedangkan pengajuan oleh staf terbuka untuk semua yang login. |
| 3 | `app/Http/Controllers/AssetReturnController.php` baris 81 (tag FR-05 #3) | `$validated = $request->validate([...])`, khususnya `'photo_proof_1' => 'required|image|max:2048'` | Variabel $validated dari method validate() | Ini variabel hasil validasi. Staf wajib memilih kondisi dan mengunggah minimal satu foto bukti. |
| 4 | `app/Http/Controllers/AssetReturnController.php` baris 105 (tag FR-05 #4) | `if ($assetRequest->status === 'pending_return') ... if ($assetRequest->status !== 'approved' || $assetRequest->returned_at)` | Kondisi if (anti pengembalian ganda) | Ini kondisi if untuk mencegah pengembalian ganda. |
| 5 | `app/Http/Controllers/AssetReturnController.php` baris 124 (tag FR-05 #5) | `AssetReturn::create([... 'status' => 'pending'])` | Pemanggilan method create() pada model AssetReturn | Ini penyimpanan transaksi pengembalian berstatus pending. Stok belum bertambah sampai admin memverifikasi. |
| 6 | `app/Http/Controllers/AssetReturnController.php` baris 158 (tag FR-05 #6) | `$data = $request->validate(['final_condition' => 'required|in:available,maintenance,broken', ...])` | Variabel $finalCondition | Ini variabel kondisi akhir. Admin boleh mengoreksi kondisi setelah memeriksa fisik barang, kalau tidak diubah dipakai kondisi dari staf. |
| 7 | `app/Http/Controllers/AssetReturnController.php` baris 185 (tag FR-05 #7) | `$asset->increment('quantity', $qty); $asset->update(['status' => $final])` | Kondisi if dengan method increment() | Ini kondisi if. Stok bertambah di sini, saat verifikasi, bukan saat staf mengajukan. Status aset mengikuti kondisi akhir. |
| 8 | `app/Http/Controllers/AssetReturnController.php` baris 221 (tag FR-05 #8) | `AssetHistory::create([... 'action' => 'returned'])` | Pemanggilan method create() pada model AssetHistory | Ini pencatatan riwayat pengembalian. Kondisi akhir dan denda tersimpan sebagai jejak audit. |

### FR-06 Laporan Kerusakan

Laporan kerusakan lahir dari dua jalur: otomatis (dari verifikasi pengembalian) dan manual (dari halaman aset). Jelaskan keduanya.


| No | File dan baris | Yang ditunjuk | Jenis | Yang diucapkan |
|---|---|---|---|---|
| 1 | `app/Http/Controllers/AssetReturnController.php` baris 191 (tag FR-06 #1) | `if (in_array($final, ['maintenance', 'broken'], true))` | Kondisi if dengan fungsi in_array() | Ini jalur otomatis laporan kerusakan. Kalau barang kembali rusak, sistem langsung membuat tiket perbaikan tanpa input tambahan. |
| 2 | `app/Http/Controllers/MaintenanceController.php` baris 146 (tag FR-06 #2) | `if (Gate::denies('asset.edit'))` | Kondisi if dengan method Gate::denies() | Ini jalur manual. Gate::denies menolak pengguna yang tidak punya izin asset.edit. |
| 3 | `app/Http/Controllers/MaintenanceController.php` baris 151 (tag FR-06 #3) | `$request->validate([...])` | Pemanggilan method validate() | Ini validasi tiket perbaikan. Aset harus ada, vendor dan deskripsi masalah wajib, tanggal mulai tidak boleh sebelum hari ini. |
| 4 | `app/Http/Controllers/MaintenanceController.php` baris 176 (tag FR-06 #4) | `if ($asset->status === 'deployed')` | Kondisi if (cek status aset) | Ini kondisi if. Aset yang sedang dipinjam tidak boleh diperbaiki sebelum dikembalikan. |
| 5 | `app/Http/Controllers/MaintenanceController.php` baris 189 (tag FR-06 #5) | `Maintenance::create([... 'status' => 'on_process'])` | Pemanggilan method create() pada model Maintenance | Ini pembuatan tiket perbaikan berstatus on_process. |
| 6 | `app/Http/Controllers/MaintenanceController.php` baris 202 (tag FR-06 #6) | `$asset->update(['status' => 'maintenance'])` | Pemanggilan method update() pada variabel $asset | Ini perubahan status aset. Setelah tiket dibuat, aset otomatis menjadi maintenance dan tidak bisa dipinjam. |

### FR-07 Teknisi Memantau Perbaikan


| No | File dan baris | Yang ditunjuk | Jenis | Yang diucapkan |
|---|---|---|---|---|
| 1 | `routes/web.php` baris 95 (tag FR-07 #1) | `Route::resource('maintenances', MaintenanceController::class)` | Route resource | Ini route resource untuk modul perbaikan. |
| 2 | `app/Http/Controllers/MaintenanceController.php` baris 17 (tag FR-07 #2) | `$this->middleware('can:maintenance.view')->only(['index', 'show'])` | Pemanggilan method middleware() di konstruktor | Ini middleware di konstruktor. Melihat daftar perbaikan butuh izin maintenance.view. |
| 3 | `app/Http/Controllers/MaintenanceController.php` baris 24 (tag FR-07 #3) | `public function index()` | Method (fungsi) controller | Ini method index. Teknisi memantau semua tiket perbaikan di sini, terbaru di atas. |
| 4 | `app/Http/Controllers/MaintenanceController.php` baris 223 (tag FR-07 #4) | `if (Gate::none(['asset.edit', 'maintenance.action']))` | Kondisi if dengan method Gate::none() | Ini kondisi if dengan Gate::none. Teknisi memakai izin maintenance.action dan admin memakai asset.edit, kalau keduanya tidak ada akses ditolak. |
| 5 | `app/Http/Controllers/MaintenanceController.php` baris 229 (tag FR-07 #5) | `'status' => 'required|in:on_process,completed,cancelled'` | Aturan validasi (elemen array) | Ini aturan validasi status. Hanya tiga nilai yang diterima: sedang diproses, selesai, atau dibatalkan. |
| 6 | `app/Http/Controllers/MaintenanceController.php` baris 242 (tag FR-07 #6) | `if ($request->status == 'completed')` | Kondisi if (status selesai) | Ini kondisi if untuk status selesai. Tanggal selesai dan ID teknisi diisi otomatis supaya tercatat siapa yang menyelesaikan. |
| 7 | `app/Http/Controllers/MaintenanceController.php` baris 278 (tag FR-07 #7) | `$maintenance->asset->status = 'available'` | Assignment properti ($maintenance->asset->status) | Ini assignment status aset. Setelah perbaikan selesai, aset kembali available dan bisa dipinjam. |
| 8 | `app/Http/Controllers/MaintenanceController.php` baris 312 (tag FR-07 #8) | `elseif ($request->status === 'cancelled')` | Kondisi elseif (status dibatalkan) | Ini kondisi elseif. Kalau perbaikan dibatalkan, aset ditandai broken karena belum diperbaiki. |

### FR-08 Laporan Rekapitulasi Data Aset


| No | File dan baris | Yang ditunjuk | Jenis | Yang diucapkan |
|---|---|---|---|---|
| 1 | `routes/web.php` baris 127 (tag FR-08 #1) | `Route::middleware(['can:report.view'])->group(...)` | Grup route dengan middleware | Ini grup route. Semua route laporan dikunci dengan izin report.view. |
| 2 | `app/Http/Controllers/ReportController.php` baris 58 (tag FR-08 #2) | `$statusCounts = Asset::selectRaw('status, COUNT(*) as total')->groupBy('status')` | Variabel $statusCounts (query agregat) | Ini variabel hasil query agregat. Satu query menghitung jumlah aset per status, lebih efisien daripada beberapa query terpisah. |
| 3 | `app/Http/Controllers/ReportController.php` baris 102 (tag FR-08 #3) | `public function exportPdf(Request $request)` | Method (fungsi) controller | Ini method exportPdf. Laporan PDF dibuat sesuai filter yang dipilih, dengan cabang untuk laporan peminjaman dan laporan aset. |
| 4 | `app/Http/Controllers/ReportController.php` baris 301 (tag FR-08 #4) | `$format = $request->query('format', 'xlsx')` | Variabel $format | Ini variabel format. Pengguna memilih CSV atau XLSX, dan defaultnya XLSX. |
| 5 | `app/Http/Controllers/ReportController.php` baris 307 (tag FR-08 #5) | `if ($format === 'csv')` | Kondisi if (cabang CSV) | Ini kondisi if untuk format CSV. Data ditulis baris demi baris langsung ke output. |
| 6 | `app/Http/Controllers/ReportController.php` baris 324 (tag FR-08 #6) | `return response()->stream($callback, 200, $headers)` | Statement return dengan response()->stream() | Ini statement return dengan stream. File dikirim sebagai aliran sehingga hemat memori. |
| 7 | `app/Http/Controllers/ReportController.php` baris 332 (tag FR-08 #7) | `if ($request->type === 'borrowing')` | Kondisi if (cabang jenis laporan) | Ini kondisi if untuk jenis laporan. Kalau tipenya borrowing yang dibuat laporan peminjaman, selain itu laporan aset. |
| 8 | `app/Services/SimpleXlsx.php` baris 10 (tag FR-08 #8) | `class SimpleXlsx` | Class (kelas) service | Ini class service buatan sendiri yang membuat file XLSX tanpa pustaka tambahan. |

## 4. Urutan Saran Saat Presentasi

1. Buka `routes/web.php` dan jelaskan tiga grup route: tamu, login, dan izin dinamis.
2. FR-01: tunjukkan bahwa semua izin berpusat di `User.php` (tag FR-01 #5) dan `AppServiceProvider.php` (tag FR-01 #6-7).
3. FR-02 sampai FR-04: ikuti satu aset dari dibuat, diajukan, sampai disetujui. Stok berubah di `approve()`.
4. FR-05 sampai FR-07: lanjutkan satu aset yang sama dikembalikan rusak, otomatis jadi tiket, lalu diperbaiki teknisi sampai `available` lagi.
5. FR-08: tutup dengan laporan rekap yang merangkum semua status tadi.
6. Buka `database/migrations` bila ditanya struktur tabel: `asset_requests`, `asset_returns`, `maintenances`, `asset_histories`.

Alur satu aset ini saling terhubung lewat kolom `status` pada tabel `assets`:

| Status aset | Diubah oleh | File |
|---|---|---|
| `available` | Aset dibuat atau perbaikan selesai | `AssetController.php`, `MaintenanceController.php` |
| `deployed` | Peminjaman disetujui dan stok habis | `BorrowingController.php` (`approve()`, tag FR-04 #5) |
| `broken` / `maintenance` | Verifikasi pengembalian atau tiket perbaikan dibuat | `AssetReturnController.php`, `MaintenanceController.php` |
| `available` lagi | Perbaikan `completed` | `MaintenanceController.php` (`update()`, tag FR-07 #7) |

## 5. Pertanyaan yang Mungkin Ditanyakan

| Pertanyaan | Jawaban singkat | Tunjukkan di |
|---|---|---|
| Kenapa memakai transaksi database? | Supaya stok, status, dan riwayat berubah bersamaan. Jika ada error di tengah, semuanya dibatalkan. | `BorrowingController.php` (`store()`, tag FR-03 #4) |
| Bagaimana mencegah dua orang meminjam barang terakhir? | `lockForUpdate()` mengunci baris aset selama transaksi. | `BorrowingController.php` (tag FR-03 #4) |
| Apa bedanya peran dan izin? | Peran punya kumpulan izin bawaan. Pengguna bisa diberi izin khusus yang menimpa peran. | `User.php` (tag FR-01 #5) |
| Kenapa stok bertambah saat verifikasi, bukan saat pengajuan pengembalian? | Barang baru dianggap kembali setelah admin memeriksa fisik dan fotonya. | `AssetReturnController.php` (`verify()`, tag FR-05 #7) |
| Siapa yang tahu aset ini pernah diubah siapa? | Tabel `asset_histories` mencatat aksi, pelaku, dan catatan. | `app/Models/AssetHistory.php` |
| Apakah kode ini aman dari input jahat? | Semua input divalidasi di server, akses dijaga Gate, dan form dilindungi token CSRF bawaan Laravel. | Metode `validate` di tiap controller |
| Kenapa password lemah ditolak saat tambah user? | `Password::defaults()` mewajibkan minimal 8 karakter, huruf besar dan kecil, angka, simbol, dan mengecek daftar password bocor. | `AppServiceProvider.php` (tag FR-01 #8) |
