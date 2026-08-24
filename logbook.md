# Logbook Aktivitas Pengembangan SIMASET
Periode: 5 Januari 2026 - 13 Februari 2026

## Minggu 1 (5 - 9 Januari 2026)
### Inisialisasi & Perancangan Sistem
- **5 Januari (Senin)**: 
    - Setup awal project Laravel 10 dengan `composer create-project`.
    - Konfigurasi environment database (`.env`) dan koneksi MySQL (DB: `simaset_db`).
    - Instalasi dependensi front-end: npm, TailwindCSS, dan template AdminLTE.
- **6 Januari (Selasa)**: 
    - Perancangan ERD awal dan pembuatan file Migration untuk tabel: `users`, `roles`, `permissions`, `assets`.
    - Implementasi relasi dasar *One-to-Many* (User -> Assets).
- **7 Januari (Rabu)**: 
    - Pembuatan `RoleSeeder` dan `UserSeeder` untuk mengisi data awal (Admin, Staff, Manager).
    - Verifikasi struktur tabel di database menggunakan phpMyAdmin/HeidiSQL.
- **8 Januari (Kamis)**: 
    - Implementasi Model `Asset` dan `AssetController`.
    - Pembuatan halaman Index (List Aset) dengan tabel statis HTML.
- **9 Januari (Jumat)**: 
    - Integrasi layout *Master Blade* (Header, Sidebar, Footer).
    - Penyesuaian navigasi sidebar berdasarkan Role user.

## Minggu 2 (12 - 16 Januari 2026)
### Manajemen Aset (CRUD) & Autentikasi
- **12 Januari (Senin)**: 
    - Implementasi fitur **Login** custom (bukan Breeze/Jetstream) dengan redirect logic berdasarkan role.
    - Implementasi Middleware `IsAdmin` dan `CheckRole` untuk proteksi rute.
- **13 Januari (Selasa)**: 
    - Pengerjaan fitur **Create Asset**: Form input data aset dengan validasi server-side.
    - Integrasi fitur **Upload Gambar Aset** (storage link setup).
- **14 Januari (Rabu)**: 
    - Pengerjaan fitur **Edit Asset**: Form update data dengan *pre-filled* value.
    - Validasi unik pada Serial Number saat update (ignoring self ID).
- **15 Januari (Kamis)**: 
    - Implementasi fitur **Delete Asset** dengan konfirmasi (SweetAlert/Modal JS).
    - Penerapan Soft Deletes pada model Aset untuk mencegah kehilangan data tidak sengaja.
- **16 Januari (Jumat)**: 
    - Penambahan fitur **Pencarian Aset (Search)** pada navigasi atas dan halaman index.
    - Penambahan fitur **Filter** aset berdasarkan Kategori dan Status (Available, Deployed, Maintenance).

## Minggu 3 (19 - 23 Januari 2026)
### Modul Peminjaman & Komunikasi (Chat)
- **19 Januari (Senin)**: 
    - Pembuatan tabel `asset_requests` (peminjaman) dan `borrowings` (detail durasi).
    - Implementasi Form Pengajuan Peminjaman di sisi User (Karyawan).
- **20 Januari (Selasa)**: 
    - Integrasi fitur **Chat Internal**.
    - **[Bug Fix]**: Memperbaiki tampilan *Bubble Chat* yang overflow teks-nya (CSS `break-word`).
- **21 Januari (Rabu)**: 
    - **[Bug Fix]**: Mengaktifkan tombol `+` (Attachment) di chat yang sebelumnya tidak merespon klik.
    - Implementasi fitur "Share Asset via Chat".
- **22 Januari (Kamis)**: 
    - Refactoring komponen Modal Pilih Aset (Asset Picker) agar reusable (bisa dipakai di Chat & Request).
- **23 Januari (Jumat)**: 
    - Pengembangan Dashboard **Maintenance**: Menambahkan tab/filter status (Pending, In-Progress, Completed).

## Minggu 4 (26 - 30 Januari 2026)
### Refactoring UI & Validasi Logic
- **26 Januari (Senin)**: 
    - Pengerjaan sampingan: Refinement UI Portfolio Website (Ensskin) - memastikan responsivitas mobile.
- **27 Januari (Selasa)**: 
    - **Debugging**: Memperbaiki fallback image pada detail aset (jika gambar null, tampilkan placeholder).
    - **Logic Fix**: Memperbaiki logika status "Overdue" (Terlambat) pada list peminjaman saya.
- **28 Januari (Rabu)**: 
    - **UI Refactor**: Merapikan halaman *User Management* (Add/Edit User) agar lebih compact, mengurangi whitespace berlebih.
- **29 Januari (Kamis)**: 
    - Implementasi **Guide ID Validation**: Menggunakan AJAX untuk cek availability judul guide secara realtime.
    - Menambahkan indikator visual (teks merah) jika ID sudah terpakai.
- **30 Januari (Jumat)**: 
    - Penyelesaian modul **Guides & Knowledge Base** (CRUD Guide List, penambahan Step-by-step instructions).

## Minggu 5 (2 - 6 Februari 2026)
### Reporting & Warehouse Module
- **2 Februari (Senin)**: 
    - Implementasi fitur **Export Laporan PDF** menggunakan library `dompdf`.
    - Implementasi fitur **Export Laporan Excel** menggunakan library `maatwebsite/excel`.
- **3 Februari (Selasa)**: 
    - Pengembangan Modul **Warehouse (Gudang)**: Dashboard stok, lokasi rak.
    - Implementasi form Mutasi Aset (Perpindahan lokasi/pemegang).
- **4 Februari (Rabu)**: 
    - Integrasi **Chart Analytics** (ApexCharts/Chart.js) di dashboard Admin.
    - Menampilkan Pie Chart (Komposisi Aset) dan Bar Chart (Statistik Peminjaman Bulanan).
- **5 Februari (Kamis)**: 
    - **Optimasi Query**: Refactoring Controller untuk menggunakan Eager Loading (`with(['user', 'asset'])`) pada semua query listing.
- **6 Februari (Jumat)**: 
    - Migrasi tabel aset ke **Server-Side Datatables** (AJAX) untuk menangani load data ribuan baris dengan cepat.

## Minggu 6 (9 - 13 Februari 2026) - [FOKUS MINGGU INI]
### Verifikasi Pengembalian, Denda, & Dokumentasi Diagrams
- **9 Februari (Senin)**: 
    - Update Schema: `php artisan make:migration add_photo_and_fine_to_returns`.
    - Backend: Update `AssetReturnController` (store & verify methods) untuk support upload bukti & input denda.
- **10 Februari (Selasa)**: 
    - Frontend User: Update UI form pengembalian (Wajib upload foto kondisi barang).
    - Frontend Admin: Update Modal Verifikasi Pengembalian (Input nominal Denda jika kondisi rusak).
- **11 Februari (Rabu)**: 
    - Dokumentasi: Membuat **Activity Diagram** (Maintenance Flow & Return Flow).
    - Dokumentasi: Membuat **Sequence Diagram** (Borrowing Flow & Return Verification Flow).
- **12 Februari (Kamis)**: 
    - Visualisasi ERD: Generate **ERD Mermaid.js**.
    - Visualisasi ERD: Konversi ke **Manual SVG** (Chen Notation) untuk layout yang lebih rapi dan keterbacaan tinggi.
    - Finalisasi ERD: Mengubah style ke **Crow's Foot Notation** dengan garis orthogonal (tegak lurus) dan label relasi Bahasa Indonesia.
- **13 Februari (Jumat)**: 
    - **Final Testing**: Memastikan alur Peminjaman -> Pengembalian (Rusak) -> Denda tercatat di database.
    - **Logbook Update**: Melengkapi dokumen `logbook.md` dengan rincian harian.
    - **Seeder Update**: Menambahkan script seeder untuk generate dummy activity log harian (1-13 Feb).
