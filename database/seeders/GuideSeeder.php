<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Guide;
use App\Models\GuideStep;
use Illuminate\Support\Facades\Schema;

class GuideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $guides = [
            // 1. APPROVAL & VERIFIKASI
            [
                'id' => 'approval-verification',
                'title' => 'Approval & Verifikasi',
                'description' => 'Proses persetujuan peminjaman dan verifikasi pengembalian barang.',
                'icon' => 'clipboard-check', 
                'color' => 'indigo', 
                'roles' => ['admin', 'super_admin'],
                'steps' => [
                    [
                        'title' => 'Cek Request Masuk',
                        'description' => "Navigasi: Sidebar > Transaksi > Approval Peminjaman\n\nAdmin wajib memeriksa notifikasi request peminjaman setiap hari. Pastikan stok fisik tersedia sebelum melakukan approval.\nStatus awal permohonan adalah 'PENDING'.",
                        'image' => null
                    ],
                    [
                        'title' => 'Proses Persetujuan (Approve/Reject)',
                        'description' => "Langkah:\n1. Klik tombol 'Detail' pada item request.\n2. Review tanggal pinjam dan keperluan.\n3. Klik 'Approve' untuk menyetujui (Stok berkurang otomatis).\n4. Klik 'Reject' jika barang tidak tersedia atau alasan tidak valid.",
                        'image' => null
                    ],
                    [
                        'title' => 'Verifikasi Pengembalian',
                        'description' => "Navigasi: Sidebar > Transaksi > Verifikasi Pengembalian\n\nSaat user mengembalikan barang:\n1. Cek kondisi fisik aset (Lecet/Rusak/Baik).\n2. Cari transaksi di menu Verifikasi.\n3. Klik 'Selesaikan' untuk mengembalikan status aset menjad 'Tersedia' (Ready).",
                        'image' => null
                    ]
                ]
            ],

            // 2. MANAJEMEN ASET
            [
                'id' => 'asset-management',
                'title' => 'Manajemen Aset',
                'description' => 'Input aset baru, cetak QR Code, mutasi lokasi, dan stok opname.',
                'icon' => 'cube', 
                'color' => 'blue', 
                'roles' => ['admin', 'super_admin', 'staff'],
                'steps' => [
                    [
                        'title' => 'Input Aset Baru',
                        'description' => "Navigasi: Sidebar > Transaksi > Input Aset Baru\n\nIsi form lengkap:\n- Nama Aset & Serial Number (Wajib Unik)\n- Kategori & Lokasi\n- Upload Foto Kondisi Terbaru\n\nSetelah simpan, QR Code akan otomatis terbuat.",
                        'image' => null
                    ],
                    [
                        'title' => 'Cetak Label QR Code',
                        'description' => "Navigasi: Sidebar > Master > Katalog Aset\n\n1. Buka detail aset.\n2. Klik tombol 'Cetak QR'.\n3. Print label dan tempelkan pada fisik barang untuk memudahkan scanning dan tracking.",
                        'image' => null
                    ],
                    [
                        'title' => 'Mutasi Lokasi (Pindah Tangan)',
                        'description' => "Navigasi: Sidebar > Transaksi > Mutasi Aset\n\nDigunakan saat memindahkan barang antar ruangan/cabang:\n1. Scan QR Barang.\n2. Pilih Lokasi Tujuan Baru.\n3. Konfirmasi Mutasi. History perpindahan akan tercatat otomatis.",
                        'image' => null
                    ]
                ]
            ],

             // 3. PENGATURAN SISTEM
             [
                'id' => 'system-settings',
                'title' => 'Pengaturan Sistem',
                'description' => 'Konfigurasi aplikasi, backup database, dan log aktivitas.',
                'icon' => 'cog', 
                'color' => 'gray', 
                'roles' => ['super_admin'],
                'steps' => [
                    [
                        'title' => 'Konfigurasi Aplikasi',
                        'description' => "Mengatur identitas instansi seperti Nama Perusahaan, Logo, dan Alamat yang akan tampil di Kop Surat Laporan Peminjaman.",
                        'image' => null
                    ],
                    [
                        'title' => 'Monitoring Log Aktivitas',
                        'description' => "Navigasi: Dashboard > Log Aktivitas\n\nSuper Admin dapat memantau seluruh aktivitas user:\n- Siapa yang login?\n- Siapa yang menghapus aset?\n- Deteksi percobaan akses ilegal.\nData ini tidak bisa dihapus demi integritas audit.",
                        'image' => null
                    ],
                    [
                        'title' => 'Backup Database',
                        'description' => "Lakukan backup rutin database MySQL melalui panel server atau menu utility (jika tersedia) untuk mencegah kehilangan data akibat kegagalan server.",
                        'image' => null
                    ]
                ]
            ],

            // 4. KELOLA PENGGUNA
            [
                'id' => 'user-management',
                'title' => 'Kelola Pengguna',
                'description' => 'Tambah user baru, reset password, dan pengaturan hak akses role.',
                'icon' => 'users', 
                'color' => 'purple', 
                'roles' => ['super_admin'],
                'steps' => [
                    [
                        'title' => 'Menambah User Baru',
                        'description' => "Navigasi: Sidebar > Utilitas > Tambah User\n\n1. Isi Nama, Email Kantor, dan Posisi.\n2. Pilih Role (Admin/Staff/User).\n3. Password default bisa diset oleh admin.",
                        'image' => null
                    ],
                    [
                        'title' => 'Pengaturan Hak Akses (Role)',
                        'description' => "Navigasi: Sidebar > Utilitas > Manajemen User > Edit\n\nAnda bisa mengatur permission spesifik user:\n- Centang 'View Only' untuk auditor.\n- Berikan akses 'Full Control' untuk Kepala Bagian.\nPastikan tidak memberikan akses Administrator ke sembarang user.",
                        'image' => null
                    ],
                    [
                        'title' => 'Reset Password & Nonaktifkan Akun',
                        'description' => "Jika karyawan resign:\n1. Buka menu Manajemen User.\n2. Edit user tersebut.\n3. Ubah status menjadi 'Non-Aktif' agar tidak bisa login lagi.\n\nJika lupa password: Klik tombol 'Reset Password' untuk generate password baru.",
                        'image' => null
                    ]
                ]
            ],

            // 5. SISTEM PEMINJAMAN (END-TO-END)
            [
                'id' => 'borrowing-flow',
                'title' => 'Sistem Peminjaman (End-to-End)',
                'description' => 'Panduan lengkap siklus peminjaman barang kantor.',
                'icon' => 'hand-raised', 
                'color' => 'green', 
                'roles' => ['all'],
                'steps' => [
                    [
                        'title' => 'Request Peminjaman',
                        'description' => "Navigasi: Katalog Aset > Pilih Barang > Klik Pinjam\n\nUser memilih barang dan menentukan durasi. Request akan masuk ke Admin untuk persetujuan.",
                        'image' => null
                    ],
                    [
                        'title' => 'Pengambilan Barang',
                        'description' => "Setelah status 'APPROVED':\nUser menemui Admin Gudang/IT untuk mengambil fisik barang. Admin melakukan serah terima.",
                        'image' => null
                    ],
                    [
                        'title' => 'Pengembalian Tepat Waktu',
                        'description' => "Wajib mengembalikan barang sebelum 'Due Date'. Keterlambatan akan tercatat di sistem dan mempengaruhi reputasi peminjaman user.",
                        'image' => null
                    ]
                ]
            ],

            // 6. PANDUAN DASAR
             [
                'id' => 'basic-guide',
                'title' => 'Panduan Dasar',
                'description' => 'Pelajari navigasi dasar, pengaturan profil, dan keamanan akun Anda.',
                'icon' => 'book-open', 
                'color' => 'teal', 
                'roles' => ['all'],
                'steps' => [
                     [
                        'title' => 'Login & Keamanan Akun',
                        'description' => "Gunakan email kantor dan password yang aman. Jangan membagikan kredensial akun kepada siapapun. Logout setelah selesai menggunakan sistem di perangkat umum.",
                        'image' => null
                    ],
                    [
                        'title' => 'Update Profil Saya',
                        'description' => "Navigasi: Klik Foto Profil > Edit Profile\n\nUser wajib melengkapi data No. HP/WhatsApp yang aktif untuk keperluan notifikasi peminjaman.",
                        'image' => null
                    ]
                ]
            ]
        ];

        // Explicitly clear tables first
        Schema::disableForeignKeyConstraints();
        GuideStep::truncate();
        Guide::truncate();
        Schema::enableForeignKeyConstraints();

        foreach ($guides as $data) {
            echo "Seeding Guide: " . $data['id'] . "\n";
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
