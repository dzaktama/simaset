# Sistem Manajemen Peminjaman Aset - Dokumentasi Implementasi

## ✅ Status Keseluruhan
Semua fitur telah berhasil diimplementasikan dan siap digunakan.

---

## 📋 Fitur yang Telah Dibuat

### 1. **BorrowingController** (`app/Http/Controllers/BorrowingController.php`)
Controller utama dengan 7 metode:

- **index()** - Daftar peminjaman dengan filter, search, dan pagination
  - Filter: status (active/returned/rejected), search (nama/aset)
  - Sorting: newest/oldest
  - Pagination: 15 items per page

- **show($id)** - Detail peminjaman dengan timeline dan countdown timer
  - Menampilkan data peminjam, aset, riwayat
  - Timeline status: diajukan → disetujui → dikembalikan/sedang dipinjam
  - Countdown timer real-time (hari/jam/menit/detik)

- **userHistory($userId)** - Riwayat peminjaman per user
  - Statistik: total, aktif, dikembalikan
  - Pagination: 20 items per page

- **return($id, Request)** - Kembalikan aset
  - Validasi kondisi (good/minor_damage/major_damage)
  - Simpan catatan pengembalian
  - Update status asset menjadi available

- **report(Request)** - Laporan dengan filter
  - Filter: tanggal, status, pengguna
  - Summary cards (total, aktif, dikembalikan)

- **exportExcel(Request)** - Export ke CSV
  - Delimiter: semicolon (;)
  - 11 kolom: No, Peminjam, Email, Aset, Tgl Peminjaman, Tgl Kembali, Status, Kondisi, Durasi, Catatan
  - UTF-8 BOM untuk kompatibilitas Excel

- **stats()** - API endpoint untuk statistik
  - Top borrowed items
  - Top borrowers
  - Total counts

### 2. **Routes** (`routes/web.php`)
7 rute untuk borrowing management:
```
GET    /borrowing                          → borrowing.index
GET    /borrowing/{id}                     → borrowing.show
PUT    /borrowing/{id}/return              → borrowing.return
GET    /borrowing/user/{userId}/history    → borrowing.user-history
GET    /borrowing/reports                  → borrowing.report
GET    /borrowing/export/excel             → borrowing.export-excel
GET    /borrowing/stats                    → borrowing.stats
```
Semua routes dilindungi dengan middleware: `['web', 'auth', 'is_admin']`

### 3. **Views**

#### a. **index.blade.php** - Daftar Peminjaman
- Statistics cards: Total, Aktif, Tertunda, Dikembalikan
- Filter form: search, status, sort
- Data table dengan 6 kolom
- Modal return dengan condition selection (radio buttons)
- Icons: SVG (no emoji)
- Responsive design (mobile-friendly)

#### b. **show.blade.php** - Detail Peminjaman
- 3-column layout (2 main + 1 sidebar)
- Cards dengan colored left border:
  - Indigo: Data Peminjam
  - Green: Data Aset
  - Blue: Timeline Peminjaman
  - Purple: Alasan Peminjaman
- Timeline dengan 4 steps (Diajukan → Disetujui → Aktif/Dikembalikan)
- **Countdown Timer** real-time:
  - Format: X hari Y jam Z menit A detik
  - Update setiap 1 detik
  - JavaScript setInterval
- Sidebar dengan:
  - Duration card (gradient orange)
  - ID card
  - Quantity card
  - Status badge
  - Action button (Kembalikan Aset)
- Detailed return modal dengan:
  - 3 pilihan kondisi (radio buttons)
  - Hover states (warna berbeda: hijau/kuning/merah)
  - Textarea untuk catatan
  - Tombol Batal/Konfirmasi

#### c. **user-history.blade.php** - Riwayat Per User
- Header dengan nama user
- Statistics cards (3 kolom):
  - Total Peminjaman (blue)
  - Aktif (green)
  - Dikembalikan (indigo)
- Data table dengan 7 kolom
- Pagination (20 items)
- SVG icons untuk setiap aset

#### d. **report.blade.php** - Laporan & Export
- Filter section:
  - Date range (dari/sampai)
  - Status dropdown
  - User dropdown
- Action buttons:
  - Filter (blue)
  - Reset (gray)
  - Download Excel (green)
  - Cetak/PDF (indigo)
- Summary cards (3 gradient):
  - Total (blue)
  - Aktif (green)
  - Dikembalikan (indigo)
- Data table (9 kolom)
- Print CSS (@media print)

### 4. **Database Migration**
File: `database/migrations/2026_01_15_000000_add_borrowing_columns_to_asset_requests.php`

Kolom yang ditambahkan ke `asset_requests` table:
- `borrowed_at` - DateTime (saat aset diambil)
- `returned_at` - DateTime (saat aset dikembalikan)
- `approved_at` - DateTime (saat permintaan disetujui)
- `condition` - String (good/minor_damage/major_damage)
- `return_notes` - Text (catatan pengembalian)
- `borrowing_status` - String (pending/active/returned/rejected)

### 5. **Model Update**
File: `app/Models/AssetRequest.php`

Kolom `fillable` ditambahkan:
```php
[
    'borrowed_at', 'returned_at', 'approved_at', 
    'condition', 'return_notes', 'borrowing_status'
]
```

### 6. **Sidebar Menu Update**
File: `resources/views/partials/sidebar.blade.php`

Menu item baru ditambahkan:
- Label: "Manajemen Peminjaman"
- Route: `route('borrowing.index')`
- Icon: SVG (lightning bolt)
- Placement: Di bawah "Laporan & Audit" (admin only)

---

## 🎨 UI/UX Features

### Icon System
- ✅ 100% SVG icons (NO emoji)
- Consistent Material Design style
- Customizable colors per context

### Color Scheme
- **Indigo** - Primary (User/Borrower info)
- **Green** - Success/Active status
- **Blue** - Information/Detail
- **Orange** - Duration/Time
- **Red** - Warning/Return action
- **Yellow** - Pending status

### Status Badges
- **Active** - Green with pulse animation
- **Returned** - Blue with checkmark
- **Pending** - Yellow with clock icon
- **Rejected** - Red with X icon

### Interactive Elements
- Modals dengan smooth fade-in
- Hover effects pada buttons & table rows
- Responsive grid layouts
- Touch-friendly button sizing

### Countdown Timer
- Real-time duration tracking
- Automatic 1-second updates
- Format: "0 hari 0 jam 0 menit 0 detik"
- Works on detail page for active borrowings

---

## 📊 Data Flow

```
1. User submits borrowing request
   ↓
2. Admin approves/rejects in dashboard
   ↓
3. If approved:
   - Status: pending → approved
   - borrowing_status: pending → active
   - borrowed_at: set to current time
   ↓
4. User can view countdown timer on detail page
   ↓
5. When returning:
   - Select condition (good/minor/major)
   - Add notes
   - Submit
   ↓
6. Update:
   - Status: approved → completed
   - returned_at: current time
   - borrowing_status: active → returned
   - Asset status: deployed → available
   ↓
7. View in report page with history
```

---

## 🔍 Search & Filter Capabilities

### Daftar Peminjaman (index)
- **Search**: Nama peminjam, nama aset
- **Status**: Active, Returned, Rejected
- **Sort**: Newest, Oldest
- **Pagination**: 15 per page

### Laporan (report)
- **Date Range**: From/To date
- **Status**: All, Active, Returned
- **User**: Dropdown selection
- **Export**: CSV dengan semicolon delimiter

### Riwayat Per User (user-history)
- **Pagination**: 20 per page
- **Display**: Automatic date formatting

---

## 📈 Statistics & Analytics

### Dashboard Cards (index & report)
```
Total Peminjaman    - Blue gradient
Peminjaman Aktif    - Green gradient
Dikembalikan        - Indigo gradient
Tertunda (index)    - Yellow gradient
```

### Per User Stats (user-history)
```
Total Borrowings
Active Borrowings
Returned Borrowings
```

### API Endpoint (stats)
```json
{
  "total_borrowings": 50,
  "active_borrowings": 12,
  "returned_borrowings": 35,
  "top_items": [...],
  "top_borrowers": [...]
}
```

---

## 🛡️ Security & Validation

### Authorization
- Only admins can access borrowing routes
- Middleware: `is_admin`

### Validation
- Condition: Required, must be good/minor_damage/major_damage
- Notes: Optional, max 500 characters
- Date filters: Valid date format

### Data Protection
- Soft deletes: Not implemented (cascade delete)
- Foreign keys: asset_id, user_id constrained
- Status tracking: All status changes recorded

---

## 📁 File Structure

```
app/
├── Http/Controllers/
│   └── BorrowingController.php (NEW - 280+ lines)
├── Models/
│   └── AssetRequest.php (UPDATED)
│
database/
├── migrations/
│   └── 2026_01_15_000000_add_borrowing_columns_to_asset_requests.php (NEW)
│
resources/views/
├── borrowing/
│   ├── index.blade.php (NEW - 230+ lines)
│   ├── show.blade.php (NEW - 380+ lines)
│   ├── user-history.blade.php (NEW - 180+ lines)
│   └── report.blade.php (NEW - 220+ lines)
├── partials/
│   └── sidebar.blade.php (UPDATED)
│
routes/
└── web.php (UPDATED - added borrowing routes)
```

---

## ✨ Highlights

### Countdown Timer Algorithm
```javascript
const diffMs = now - startDate;
const days = Math.floor(diffMs / (1000 * 60 * 60 * 24));
const hours = Math.floor((diffMs % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
const seconds = Math.floor((diffMs % (1000 * 60)) / 1000);
```
Updates every 1000ms (1 second)

### CSV Export with Semicolon Delimiter
```php
// BOM untuk UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
// Headers & data dengan semicolon delimiter
fputcsv($output, [...], ';');
```

### Modal Interaction
```javascript
function openReturnModal(borrowingId) {
    form.action = `/borrowing/${borrowingId}/return`;
    modal.classList.remove('hidden');
}

function closeReturnModal() {
    modal.classList.add('hidden');
}

// Click outside to close
modal.addEventListener('click', (e) => {
    if (e.target === modal) closeReturnModal();
});
```

---

## 🚀 Deployment Checklist

- ✅ Create migration file
- ✅ Run `php artisan migrate`
- ✅ Create BorrowingController
- ✅ Add routes to web.php
- ✅ Create 4 views (index, show, user-history, report)
- ✅ Update AssetRequest model
- ✅ Update sidebar menu
- ✅ Test routes: `php artisan route:list | grep borrowing`
- ✅ Verify sidebar displays correctly
- ✅ Test countdown timer functionality
- ✅ Test modal interactions
- ✅ Test CSV export
- ✅ Test print/PDF rendering

---

## 📝 Usage Guide

### Admin Access Borrowing Management
1. Login as admin
2. Click "Manajemen Peminjaman" in sidebar
3. Use filters to find specific borrowing
4. Click "Detail" to see full information
5. Click "Kembalikan Aset" to return asset
6. Select condition, add notes, confirm
7. View report with filters and export

### View Borrowing Statistics
- **Per User**: Click on user in any table
- **Overall Report**: Go to Laporan section
- **API**: GET /borrowing/stats (JSON)

### Export Data
- Click "Download Excel" on report page
- File downloads as CSV (semicolon-delimited)
- Open in Excel with proper UTF-8 encoding

### Print/PDF
- Click "Cetak" button
- Use browser print dialog
- Print to PDF or printer
- Automatic formatting (filters hidden, clean table)

---

## 🔧 Troubleshooting

### Countdown Timer Not Updating
- Check browser console for JS errors
- Ensure Carbon date parsing is correct
- Verify `setInterval` is running (F12 → Sources)

### CSV Export Encoding Issues
- UTF-8 BOM is included automatically
- If still showing garbled: Open in Excel with "Import Text"
- Select: UTF-8 encoding, Semicolon delimiter

### Routes Not Found
- Run: `php artisan route:clear`
- Then: `php artisan route:cache`
- Verify middleware: `is_admin`

### Modal Not Showing
- Check if `returnModal` element exists in HTML
- Verify CSS class `hidden` is in Tailwind config
- Check console for JS errors

---

## 📞 Support

Untuk pertanyaan teknis atau issues:
1. Check controller methods
2. Review view templates
3. Check browser console (F12)
4. Run `php artisan tinker` untuk debug database
5. Verify migrations: `php artisan migrate:status`

---

**Last Updated:** 15 Januari 2026
**Status:** ✅ Production Ready
