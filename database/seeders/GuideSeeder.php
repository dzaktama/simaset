<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Guide;
use App\Models\GuideStep;

class GuideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $guides = [
            // 1. MASTER DATA & DASHBOARD
            [
                'id' => 'master-data',
                'title' => 'Master Data & Dashboard',
                'description' => 'Panduan lengkap mengenai modul Master Data, pemahaman Dashboard, dan manajemen lokasi.',
                'icon' => 'cube', 
                'color' => 'blue', 
                'roles' => ['all'],
                'steps' => [
                    [
                        'title' => 'Memahami Dashboard Utama',
                        'description' => "Definisi: Dashboard adalah halaman ringkasan yang muncul pertama kali saat login.\n\nNavigasi: Sidebar > Master > Dashboard\n\nFungsi:\n1. Total Aset: Jumlah seluruh barang yang terdaftar.\n2. Aset Dipinjam: Jumlah barang yang sedang berada di tangan karyawan.\n3. Maintenance: Barang yang sedang dalam perbaikan.\n\nDianjurkan untuk mengecek grafik tren aset setiap bulan untuk evaluasi pengadaan.",
                        'image' => null
                    ],
                    [
                        'title' => 'Mengakses Katalog Aset',
                        'description' => "Definisi: Katalog Aset adalah perpustakaan digital seluruh inventaris perusahaan.\n\nNavigasi: Sidebar > Master > Katalog Aset\n\nLangkah-langkah:\n1. Buka menu 'Katalog Aset'.\n2. Gunakan kolom 'Search' di atas untuk mencari nama barang.\n3. Gunakan filter 'Kategori' untuk menyortir jenis barang (Laptop, Furniture, Kendaraan).\n4. Klik tombol 'Detail' pada barang untuk melihat spesifikasi lengkapnya.",
                        'image' => null
                    ],
                    [
                        'title' => 'Manajemen Lokasi & Stok Opname',
                        'description' => "Definisi: Fitur untuk melihat posisi fisik barang (Gudang/Lantai/Ruang).\n\nNavigasi: Sidebar > Master > Lokasi Barang\n\nLangkah-langkah:\n1. Pilih Menu 'Lokasi Barang'.\n2. Klik pada nama Ruangan (misal: 'Ruang Server').\n3. Sistem akan memfilter daftar aset yang HANYA ada di ruangan tersebut.\n4. Cocokkan jumlah fisik dengan data di layar (Stok Opname).",
                        'image' => null
                    ]
                ]
            ],

            // 2. MANAJEMEN TRANSAKSI ASET
            [
                'id' => 'transaction-asset',
                'title' => 'Transaksi & Sirkulasi Aset',
                'description' => 'Prosedur operasional untuk Input, Mutasi, dan Perbaikan Aset.',
                'icon' => 'clipboard-check', 
                'color' => 'indigo', 
                'roles' => ['staff', 'admin', 'super_admin'],
                'steps' => [
                    [
                        'title' => 'Input Aset Baru',
                        'description' => "Definisi: Mendaftarkan barang yang baru dibeli ke dalam sistem.\n\nNavigasi: Sidebar > Transaksi > Input Aset Baru\n\nLangkah-langkah:\n1. Isi 'Nama Aset' (Wajib).\n2. Masukkan 'Serial Number' (Unik, ada di bodi barang).\n3. Pilih 'Kategori' dan 'Lokasi Awal'.\n4. Upload Foto Kondisi Barang (Wajib).\n5. Klik 'Simpan'. QR Code akan otomatis ter-generate.",
                        'image' => null
                    ],
                    [
                        'title' => 'Mutasi Aset (Pindah Lokasi)',
                        'description' => "Definisi: Memindahkan hak milik/lokasi aset, misal dari Gudang Pusat ke Cabang.\n\nNavigasi: Sidebar > Transaksi > Mutasi Aset\n\nLangkah-langkah:\n1. Pilih menu 'Mutasi Aset'.\n2. Scan QR Code barang yang akan dipindah.\n3. Pilih 'Lokasi Tujuan'.\n4. Klik 'Proses Mutasi'. Status lokasi aset akan berubah real-time.",
                        'image' => null
                    ],
                    [
                        'title' => 'Pelaporan Kerusakan (Maintenance)',
                        'description' => "Definisi: Melaporkan aset yang rusak agar segera diperbaiki teknisi.\n\nNavigasi: Sidebar > Transaksi > Lapor Kerusakan\n\nLangkah-langkah:\n1. Pilih menu 'Lapor Kerusakan'.\n2. Cari Aset atau Scan QR.\n3. Deskripsikan kerusakan (contoh: 'Layar bergaris', 'AC bocor').\n4. Set Prioritas (Low/Medium/High/Critical).\n5. Submit laporannya.",
                        'image' => null
                    ]
                ]
            ],

            // 3. SIRKULASI PEMINJAMAN
            [
                'id' => 'borrowing-flow',
                'title' => 'Sistem Peminjaman (End-to-End)',
                'description' => 'Panduan lengkap siklus peminjaman barang kantor.',
                'icon' => 'hand-raised', 
                'color' => 'green', 
                'roles' => ['all'],
                'steps' => [
                    [
                        'title' => 'Langkah 1: Request Peminjaman (User)',
                        'description' => "Navigasi: Sidebar > Master > Katalog Aset > Klik 'Pinjam'\n\nLangkah-langkah:\n1. User mencari barang di katalog.\n2. Klik tombol 'Pinjam' pada kartu barang.\n3. Tentukan Tanggal Pinjam & Tanggal Kembali.\n4. Tulis Keperluan (Wajib).\n5. Kirim Request. Status: 'PENDING'.",
                        'image' => null
                    ],
                    [
                        'title' => 'Langkah 2: Verifikasi & Approval (Admin)',
                        'description' => "Navigasi: Sidebar > Transaksi > Approval Peminjaman\n\nLangkah-langkah:\n1. Admin menerima notifikasi.\n2. Buka menu Approval.\n3. Cek ketersediaan barang & durasi.\n4. Klik 'Approve' (Setujui) atau 'Reject' (Tolak).\n5. Jika Approve, status berubah menjadi 'DIPINJAM'.",
                        'image' => null
                    ],
                    [
                        'title' => 'Langkah 3: Pengembalian Barang',
                        'description' => "Navigasi: Sidebar > Utilitas > Aset Saya\n\nLangkah-langkah:\n1. User mengembalikan barang fisik ke Admin.\n2. Admin memeriksa kondisi barang.\n3. Admin mengakses menu 'Verifikasi Pengembalian'.\n4. Klik 'Selesaikan' jika barang aman. Stok kembali ke gudang.",
                        'image' => null
                    ]
                ]
            ],

            // 4. LAPORAN & AUDIT
            [
                'id' => 'reporting',
                'title' => 'Laporan & Audit',
                'description' => 'Cara menarik data untuk kebutuhan audit dan manajemen.',
                'icon' => 'book-open', 
                'color' => 'teal', 
                'roles' => ['admin', 'super_admin'],
                'steps' => [
                    [
                        'title' => 'Mengakses Pusat Data (Analytics)',
                        'description' => "Navigasi: Sidebar > Laporan > Pusat Data\n\nFungsi:\nMenampilkan grafik depresiasi nilai aset, total pengeluaran belanja aset per tahun, dan kinerja maintenance.",
                        'image' => null
                    ],
                    [
                        'title' => 'Export Laporan (Excel/PDF)',
                        'description' => "Navigasi: Sidebar > Laporan > Laporan & Audit\n\nLangkah-langkah:\n1. Pilih tipe laporan (Aset / Peminjaman / Maintenance).\n2. Filter Rentang Tanggal (Start Date - End Date).\n3. Pilih Format (PDF untuk cetak, Excel untuk olah data).\n4. Klik 'Download'.",
                        'image' => null
                    ],
                    [
                        'title' => 'Melacak Riwayat Barang',
                        'description' => "Navigasi: Sidebar > Laporan > Riwayat Pindah / Riwayat Peminjaman\n\nFungsi:\nAudit trail untuk melihat 'Siapa memegang apa' pada tanggal tertentu. Berguna saat terjadi kehilangan barang.",
                        'image' => null
                    ]
                ]
            ],

             // 5. UTILITY & USER
             [
                'id' => 'user-management',
                'title' => 'Utilitas & Manajemen Akun',
                'description' => 'Fitur pendukung personal dan administrasi pengguna.',
                'icon' => 'users', 
                'color' => 'purple', 
                'roles' => ['all'],
                'steps' => [
                    [
                        'title' => 'Chat & Diskusi Internal',
                        'description' => "Navigasi: Sidebar > Transaksi > Pesan & Diskusi\n\nFungsi:\nMenghubungi admin atau sesama user terkait ketersediaan barang tanpa aplikasi chatting eksternal.",
                        'image' => null
                    ],
                    [
                        'title' => 'Cek Aset Tanggungan (Aset Saya)',
                        'description' => "Navigasi: Sidebar > Utilitas > Aset Saya\n\nFungsi:\nDaftar barang yang SAAT INI sedang Anda pinjam. Harap dikembalikan sebelum tanggal jatuh tempo.",
                        'image' => null
                    ],
                    [
                        'title' => 'Kelola Pengguna (Khusus Super Admin)',
                        'description' => "Navigasi: Sidebar > Utilitas > Manajemen User\n\nFungsi:\n1. Tambah User Baru.\n2. Reset Password User lain.\n3. Ubah Role (Jadikan Admin/Staff).\n4. Nonaktifkan akun karyawan resign.",
                        'image' => null
                    ]
                ]
            ],
            
            // 6. FAQ
             [
                'id' => 'faq-troubleshoot',
                'title' => 'Bantuan & FAQ',
                'description' => 'Pertanyaan umum dan kendala teknis.',
                'icon' => 'question-mark-circle', 
                'color' => 'gray', 
                'roles' => ['all'],
                'steps' => [
                     [
                        'title' => 'Tidak Bisa Login / Lupa Password',
                        'description' => "Solusi:\nSistem ini tidak memiliki fitur 'Forgot Password' mandiri demi keamanan korporat. Hubungi Tim IT Support untuk reset manual.",
                        'image' => null
                    ],
                    [
                        'title' => 'Kamera Scanner Blank',
                        'description' => "Penyebab:\nBrowser memblokir akses ke kamera.\n\nSolusi:\n1. Cek ikon gembok di URL bar.\n2. Klik 'Site Settings'.\n3. Ubah Camera menjadi 'Allow'.\n4. Refresh halaman.",
                        'image' => null
                    ]
                ]
            ],
        ];

        foreach ($guides as $data) {
            $steps = $data['steps'];
            unset($data['steps']);

            $guide = Guide::updateOrCreate(
                ['id' => $data['id']],
                $data
            );
            
            // Re-seed steps logic: Delete old to ensure order/content sync
            $guide->steps()->delete();

            foreach ($steps as $index => $step) {
                GuideStep::create([
                    'guide_id' => $guide->id,
                    'title' => $step['title'],
                    'description' => $step['description'],
                    'image' => $step['image'],
                    'order_index' => $index
                ]);
            }
        }
    }
}
