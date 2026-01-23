# PENJELASAN LENGKAP FITUR SIMASET

Halo! Dokumen ini adalah panduan lengkap seluruh fitur yang ada di aplikasi Simaset, mencakup semua perubahan terbaru yang sudah kita terapkan. Penjelasan ini dibuat sedetail mungkin dengan bahasa yang santai biar enak dibaca.

---

## 🏗️ 1. DATA MASTER (Pondasi Aplikasi)

Bagian ini adalah tempat kita ngatur data-data mentah sebelum bisa dipakai transaksi.

### A. Kategori Barang (Bahasa Indonesia)
*   **Apa itu?**: Pengelompokan aset biar rapi.
*   **Fitur**: Semua kategori sekarang pakai Bahasa Indonesia yang baku. Contoh: "Elektronik", "Perabot", "Kendaraan".
*   **Fungsi**: Pas nambah aset baru, user tinggal pilih dari dropdown. Gak perlu ngetik manual lagi, jadi datanya seragam.

### B. Detail Peminjam (User)
*   **Halaman**: Manajemen Pengguna.
*   **Fitur Baru**: Sekarang admin bisa lihat profil lengkap peminjam.
*   **Koneksi**: Di setiap riwayat peminjaman, kalau nama user diklik, langsung lompat ke profil detailnya. Jadi admin tau, "Ooh, si Budi ini anak divisi IT toh".

### C. Alur Peminjaman (Timeline Style ala ShopeeFood) 🛵
Ini fitur keren di halaman **Detail Peminjaman**. Kita gak cuma nampilin tabel kaku, tapi pakai visualisasi garis waktu.
*   **Tampilan**: Ada garis vertikal dengan titik-titik status.
*   **Warna-warni**:
    *   **Abu-abu**: Belum kejadian (Masa depan).
    *   **Biru/Hijau**: Sudah kejadian (Selesai).
*   **Isi Timeline**:
    1.  **Pengajuan**: Kapan user minta barang (Tanggal & Jam).
    2.  **Disetujui Admin**: Kapan admin klik ACC.
    3.  **Barang Diambil**: Kapan barang pindah tangan.
    4.  **Dikembalikan**: Kapan barang balik ke gudang.
*   **Tombol Pintas**: Kalau statusnya masih *"Menunggu Persetujuan"*, di situ langsung ada tombol **"Setujui"** atau **"Tolak"**. Admin gak perlu pindah halaman buat nge-ACC.

### D. Durasi & Countdown (Hitung Mundur) ⏳
*   **Masalah Lama**: Dulu hitungan harinya kacau (bisa minus desimal panjang banget).
*   **Solusi Sekarang**:
    *   **Format Manusia**: "3 Hari 4 Jam" (Bukan 3.14159 hari).
    *   **Logic Countdown**: Menghitung **SISA WAKTU**.
        *   Misal pinjam 3 hari, baru jalan 1 hari. Maka tulisannya: "Sisa 2 Hari Lagi".
        *   Kalau telat? Warnanya jadi merah: "Terlambat 1 Hari".

### E. Peta Lokasi (Warehouse Map ala KAI Access) 🗺️
Ini fitur visual buat admin gudang.
*   **Konsep**: Bayangkan denah kursi kereta api di aplikasi KAI. Kita terapkan itu buat Rak Gudang.
*   **Kotak-kotak**:
    *   Tiap kotak mewakili satu **Rak**.
    *   **Kotak Putih**: Rak Kosong.
    *   **Kotak Biru**: Ada barangnya.
    *   **Kotak Merah**: Ada barang rusak di situ (Warning!).
*   **Interaktif**: Kalau kotaknya diklik, muncul popup daftar barang apa aja yang ada di rak itu. Canggih kan?

---

## 🛒 2. DATA TRANSAKSI (Aktivitas Harian)

### A. Input Aset Baru
*   **Kategori Wajib**: Saat input barang, field Kategori gak boleh kosong.
*   **Serial Number Otomatis (Smart Gen)**:
    *   Sistem otomatis bikin nomor unik biar admin gak pusing.
    *   **Rumus**: `[3 Huruf Nama] - [5 Angka Urut]`.
    *   Contoh: Input "Laptop Dell". Sistem ambil "LAP". Cek database angka terakhir berapa. Jadinya: **LAP-00001**. Rapi dan terbaca.

### B. Manajemen Peminjaman
*   **Detail User**: Seperti poin 1.B, di tabel transaksi pun nama user bisa diklik buat intip profilnya.

---

## 📊 3. LAPORAN (Reporting)

Fitur ini buat laporan ke atasan.

### A. Laporan Aset & Peminjaman (PDF)
*   **Tampilan**: Mirip preview pas mau nge-print dokumen.
*   **Filter Tanggal**:
    *   Bisa pilih: "Saya mau laporan dari tanggal 1 Januari sampai 31 Januari".
    *   Sistem bakal nyaring data cuma di tanggal itu aja.
*   **Fitur Download**:
    *   **Preview**: Liat dulu di layar (iframe).
    *   **Download PDF**: Klik tombol, langsung tersimpan file `.pdf` rapi siap kirim email.
*   **Desain**: Kita samain layout laporan Aset dan Laporan Peminjaman biar konsisten (Header logo, judul, tabel rapi), cuma beda isinya aja.

---

## 🛠️ 4. UTILITAS (Pengaturan Sistem)

### A. Manajemen Grup & Role (Hak Akses)
Kita bagi pengguna jadi 4 kasta, masing-masing punya kekuatan beda:

1.  **Super Admin (Dewa)** 👑
    *   Bisa segalanya.
    *   Bisa bikin user baru, reset password orang, hapus admin.
    *   **Fitur Spesial**: *Role Switcher*. Super admin bisa "nyamar" jadi User biasa atau Service Center buat ngetes tampilan mereka kayak gimana.
2.  **Admin (Manajer)** 👔
    *   Bisa atur aset (Tambah/Edit).
    *   Bisa ACC peminjaman.
    *   **TAPI**: Gak bisa otak-atik data User (gak bisa pecat orang).
3.  **Service Center (Tukang Bengkel)** 🔧
    *   Role baru nih!
    *   Tugasnya cuma satu: **Perbaiki Barang**.
    *   Bisa update status aset dari "Rusak" ke "Ready".
    *   Bisa akses menu Maintenance.
    *   Gak bisa liat data User atau Transaksi Peminjaman (Fokus kerjaan teknis aja).
4.  **User (Karyawan)** 👷
    *   Cuma bisa liat katalog barang.
    *   Request pinjam barang.
    *   Chat tanya admin.

### B. Pengaturan Profil Saya 👤
Setiap user sekarang punya halaman profil sendiri.
*   **Ganti Foto**: Biar gak bosen liat avatar default.
*   **Ganti Password**: Fitur wajib buat keamanan.
*   **Info Dasar**: Update NIP, No HP, Divisi.

---

Semoga penjelasan naskah lengkap ini membantu memahami betapa komplitnya sistem Simaset yang sudah kita bangun! 🚀
