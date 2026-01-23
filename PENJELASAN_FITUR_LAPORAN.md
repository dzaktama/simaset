# Penjelasan Detail Source Code Fitur Cetak Laporan (PDF)

Halo! Dokumen ini akan membedah cara kerja fitur **Cetak Laporan** di aplikasi Simaset. Saya akan jelaskan langkah demi langkah, dari tampilan form sampai jadi file PDF, dengan bahasa yang santai.

Fitur ini menggunakan library bernama **DomPDF**, yang tugasnya mengubah kode HTML (tampilan web) menjadi file PDF siap cetak.

---

## 🏗️ Komponen Utama (File-file yang Terlibat)

Ada 3 bagian penting dalam fitur ini:

1.  **View (Tampilan Form)**: `resources/views/reports/index.blade.php`
    *   Tempat user milih mau laporan apa (Aset atau Peminjaman), filter tanggal, kategori, dll.
2.  **Controller (Otak)**: `app/Http/Controllers/ReportController.php`
    *   Menerima request filter dari user, mengambil data dari database, dan memproses PDF.
3.  **View PDF (Kertas Hasil)**: `resources/views/pdf/`
    *   `assets_report.blade.php`: Desain kertas laporan Aset.
    *   `borrowing_report.blade.php`: Desain kertas laporan Peminjaman.

---

## 🚀 1. Tampilan Form (`reports/index.blade.php`)
Saat Anda membuka menu "Laporan", file ini yang bekerja.

*   **Fungsi Utama**: Menampilkan form filter yang lengkap.
*   **Fitur Live Preview**:
    *   Di sebelah kiri ada form filter (Tanggal, Kategori, Status).
    *   Di sebelah kanan ada `<iframe id="pdfPreview">`.
    *   Setiap kali Anda ganti filter (misal ganti kategori), Javascript akan otomatis me-refresh iframe tersebut. Jadi Anda bisa lihat hasil laporannya sebelum didownload.
*   **Tombol Download**: Saat diklik, dia mengirim form yang sama tapi dengan perintah "Download Dong", bukan cuma "Preview".

---

## 🧠 2. Otak Sistem (`ReportController.php`)
Ini komandan lapangan. Fungsi utamanya ada di `exportPdf()`.

### A. Persiapan Data (`commonData`)
Sebelum bikin laporan, controller nyiapin data wajib:
*   **Logo Perusahaan**: Diambil dari folder public, diubah jadi kode Base64 biar bisa nempel di PDF.
*   **Tanggal Cetak**: Biar ketauan kapan laporan ini dibuat.
*   **Judul Custom**: Kalau user ngetik judul sendiri di form.

### B. Percabangan Tipe Laporan
Controller ngecek: *"User minta laporan apa nih?"*

**KASUS 1: Laporan Peminjaman (`type == 'borrowing'`)**
1.  Query ke tabel `AssetRequest` (Data Peminjaman).
2.  Filter berdasarkan **Tanggal Start** sampai **End**.
3.  Filter status (misal: cuma mau liat yang 'Active' atau 'Dikembalikan').
4.  Panggil desain kertas: `pdf.borrowing_report`.

**KASUS 2: Laporan Aset/Stok (`type == 'asset'`) - Default**
1.  Query ke tabel `Asset`.
2.  Filter berdasarkan **Search**, **Kategori**, atau **Status**.
3.  **Logika Gambar**:
    *   Ada filter "Tampilkan Gambar".
    *   Jika YA, controller akan mengubah file gambar aset dari disk menjadi kode Base64 agar DomPDF bisa membacanya. Kalau tidak diubah, biasanya gambarnya silang (broken) di PDF.
4.  Panggil desain kertas: `pdf.assets_report`.

### C. Proses Cetak (Rendering)
Setelah data siap, baris sakti ini dijalankan:
```php
$pdf = Pdf::loadView('pdf.assets_report', $data);
```
Artinya: *"Tolong ambil data $data, masukkan ke desain kertas 'assets_report', lalu 'masak' jadi PDF."*

### D. Output (Download vs Stream)
*   Kalau user klik tombol **Download**: Controller maksa browser buat download file.
*   Kalau cuma **Preview** (iframe): Controller menampilkan PDF-nya di browser (Stream).

---

## 📄 3. Desain Kertas (`resources/views/pdf/...`)
Ini adalah template HTML biasa yang didesain khusus buat kertas A4.

*   **Styling Khusus**: Menggunakan CSS sederhana (bukan Tailwind/Bootstrap yang berat), karena DomPDF agak "jadul" dan pilih-pilih soal CSS.
*   **Page Break**: Ada kode CSS `page-break-after: always` untuk memastikan kalau datanya banyak, dia pindah halaman dengan rapi.
*   **Tabel**: Menggunakan tag `<table>` standar HTML buat nampilin baris-baris data.

---

## 🔄 Rangkuman Alur Kerja

1.  **User**: Buka menu Laporan, pilih "Kategori: Laptop", klik "Preview".
2.  **View (Blade)**: Mengirim request ke URL `/reports/pdf?category=laptop`.
3.  **Controller**:
    *   Tangkap `category=laptop`.
    *   Ambil semua aset yang kategorinya Laptop dari database.
    *   Siapkan gambar logo & data aset.
    *   Render ke PDF.
4.  **Browser**: Menampilkan PDF di dalam kotak iframe.
5.  **User**: Klik "Download PDF".
6.  **Controller**: Melakukan hal yang sama, tapi kali ini menyuruh browser menyimpan filenya sebagai `Laporan_Aset_2026.pdf`.

Semoga penjelasannya mudah dimengerti! Kalau ada yang bingung di bagian mana, tanya aja ya! 😊
