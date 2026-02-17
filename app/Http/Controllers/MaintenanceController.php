<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    public function __construct()
    {
        // Permission check moved to individual methods for better UX (Redirect + Message)
        $this->middleware('can:maintenance.view')->only(['index', 'show']);
    }
    /**
     * Display a listing of the maintenance logs.
     */
    public function index()
    {
        $maintenances = Maintenance::with('asset')
            ->latest('start_date')
            ->paginate(10);

        return view('maintenances.index', [
            'title' => 'Riwayat Perbaikan Aset',
            'maintenances' => $maintenances
        ]);
    }

    /**
     * Show the form for creating a new maintenance record.
     */
    public function create(Request $request)
    {
        // [SECURITY] Cegah Karyawan input perbaikan manual
        if (\Illuminate\Support\Facades\Gate::denies('asset.edit')) {
            return redirect()->route('dashboard')->with('error', 'Akses Ditolak! Hanya Teknisi/Admin yang boleh membuat tiket maintenance manual. Silakan gunakan fitur "Lapor Kerusakan" pada detail aset.');
        }
        $asset = null;
        $maintenanceError = null;
        
        if ($request->has('asset_id')) {
            $asset = Asset::find($request->asset_id);
            
            if ($asset) {
                // VALIDASI KETAT: Hanya status 'broken' yang boleh dibuat tiket perbaikan
                switch ($asset->status) {
                    case 'maintenance':
                        // Cek apakah ada tiket perbaikan aktif untuk aset ini
                        $activeTicket = Maintenance::where('asset_id', $asset->id)
                            ->where('status', 'on_process')
                            ->first();
                        
                        if ($activeTicket) {
                            return redirect()->route('maintenances.show', $activeTicket)
                                ->with('warning', 'Aset ini sudah memiliki tiket perbaikan aktif (ID: #' . $activeTicket->id . '). Selesaikan tiket yang ada sebelum membuat tiket baru.');
                        }
                        // Jika tidak ada tiket aktif tapi status maintenance (inkonsisten), redirect ke katalog
                        return redirect()->route('assets.index')
                            ->with('error', 'Aset ini sudah dalam status perbaikan. Periksa daftar tiket perbaikan.');
                        
                    case 'available':
                        return redirect()->route('assets.index')
                            ->with('error', 'Aset dengan status "Available" tidak perlu diperbaiki karena masih berfungsi normal.');
                        
                    case 'deployed':
                        return redirect()->route('assets.index')
                            ->with('error', 'Aset dengan status "Deployed" sedang digunakan. Tarik aset terlebih dahulu jika ingin diperbaiki.');
                        
                    case 'broken':
                        // Status valid, lanjutkan ke form
                        break;
                        
                    default:
                        return redirect()->route('assets.index')
                            ->with('error', 'Status aset tidak valid untuk perbaikan.');
                }
            }
        }
        
        // [OPTIMASI] Hapus load semua aset yang berat
        // $assets = Asset::with('holder')->where('status', 'broken')->get();
        // Kita kirim array kosong, nanti Select2 yang handle via AJAX

        return view('maintenances.create', [
            'title' => 'Input Perbaikan Baru',
            'assets' => [], // Empty by default
            'selectedAsset' => $asset,
            'maintenanceError' => $maintenanceError
        ]);
    }

    /**
     * Display the specified maintenance record.
     */
    public function show(Maintenance $maintenance)
    {
        // DATA PREPARATION FOR RACK PICKER MODAL
        $assetsData = \App\Models\Asset::orderBy('name')
            ->get()
            ->map(function($asset) {
                return [
                    'id' => $asset->id,
                    'name' => $asset->name,
                    'serial_number' => $asset->serial_number,
                    'location' => $asset->location ?? 'Belum ada lokasi',
                    'image' => $asset->image ? asset('storage/' . $asset->image) : null,
                    'category' => $asset->category,
                    'brand' => $asset->brand,
                    'initial' => substr($asset->name, 0, 2),
                    'status' => $asset->status,
                    'lorong' => $asset->lorong,
                    'rak' => $asset->rak
                ];
            });

        $racksArray = [];
        for ($i = 1; $i <= 50; $i++) {
            $racksArray[] = 'R-' . str_pad($i, 2, '0', STR_PAD_LEFT);
        }
        $areasArray = range('A', 'Z');

        return view('maintenances.show', [
            'title' => 'Detail Perbaikan',
            'maintenance' => $maintenance->load(['asset.holder', 'user']),
            'assetsData' => $assetsData,
            'racksArray' => $racksArray,
            'areasArray' => $areasArray
        ]);
    }

    /**
     * Store a newly created maintenance record in storage.
     */
    public function store(Request $request)
    {
        // [SECURITY]
        if (\Illuminate\Support\Facades\Gate::denies('asset.edit')) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'vendor_name' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'problem_description' => 'required|string',
            'cost' => 'nullable|numeric|min:0'
        ], [
            'start_date.after_or_equal' => 'Tanggal mulai service tidak boleh kurang dari hari ini.'
        ]);

        DB::beginTransaction();
        try {
            // 1. Ambil Data Aset Dulu (Fix Undefined Variable)
            $asset = Asset::findOrFail($request->asset_id);

            $description = $request->problem_description;
            if ($request->filled('priority')) {
                $priorityLabel = ucfirst($request->priority);
                $description = "[Prioritas: {$priorityLabel}] " . $description;
            }

            // Validasi Status: Jangan izinkan maintenance jika aset sedang 'deployed' (dipinjam user)
            // Kecuali Super Admin yang mungkin punya policy bypass (tapi logic standar tetap harus return dulu)
            if ($asset->status === 'deployed') {
                return back()->with('error', 'Aset sedang dipinjam (Status: Deployed). Harap proses pengembalian terlebih dahulu.');
            }

            Maintenance::create([
                'asset_id' => $request->asset_id,
                'user_id' => auth()->id(), // Teknisi yang membuat tiket
                'vendor_name' => $request->vendor_name,
                'start_date' => $request->start_date,
                'problem_description' => $description,
                'cost' => $request->cost ?? 0,
                'status' => 'on_process'
            ]);

            // 2. Update Asset Status
            $asset->update(['status' => 'maintenance']);

            DB::commit();
            return redirect()->route('maintenances.index')->with('success', 'Aset berhasil masuk status Perbaikan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     * Use this to update progress or complete the maintenance.
     */
    public function update(Request $request, Maintenance $maintenance)
    {
        // [SECURITY]
        if (\Illuminate\Support\Facades\Gate::denies('asset.edit')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk mengubah status perbaikan.');
        }
        $request->validate([
            'status' => 'required|in:on_process,completed,cancelled',
            'vendor_name' => 'nullable|string',
            'start_date' => 'nullable|date',
            'problem_description' => 'nullable|string',
            'completion_date' => 'nullable|date', // Validasi strict dihapus, logic pindah ke bawah
            'cost' => 'nullable|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            // LOGIC BARU: Auto Timestamp & Prevention Error
            if ($request->status == 'completed') {
                $completionDate = now();
                $request->merge(['completion_date' => $completionDate]);
                $request->merge(['resolver_id' => auth()->id()]); // Set Resolver ID

                // Cek konflik: Jika selesai SEBELUM mulai, maka majukan tanggal mulai ke saat ini juga
                // Ini mencegah error logic "Selesai sebelum mulai"
                $currentStartDate = $maintenance->start_date;
                if ($request->has('start_date')) {
                    $currentStartDate = \Carbon\Carbon::parse($request->start_date);
                }

                if ($completionDate->lt($currentStartDate)) {
                    // Auto-adjust start date agar masuk akal
                    $request->merge(['start_date' => $completionDate]);
                }
            }

            $maintenance->update($request->all());

            // Jika Selesai, kembalikan status aset
            // Jika Selesai, kembalikan status aset
            if ($request->status === 'completed') {
                $maintenance->asset->status = 'available';

                // CEK JIKA ADA REQUEST MUTASI (Deferred Mutation)
                if ($request->filled('target_location')) {
                    $targetLocation = $request->target_location;

                    // Parse Location (Format: "Area X - Rak R-01")
                    $pattern = '/Area ([A-Z]) - Rak (R-\d+)/';
                    if (preg_match($pattern, $targetLocation, $matches)) {
                        $newLorong = 'Area ' . $matches[1];
                        $newRak = $matches[2];

                        // Update Asset Location
                        $maintenance->asset->location = $targetLocation;
                        $maintenance->asset->lorong = $newLorong;
                        $maintenance->asset->rak = $newRak;

                        // Catat History Perpindahan
                        \App\Models\AssetHistory::create([
                            'asset_id' => $maintenance->asset_id,
                            'user_id' => auth()->id(),
                            'action' => 'moved',
                            'notes' => 'Mutasi dari Maintenance: ' . ($request->mutation_notes ?? 'Perpindahan lokasi pasca perbaikan.'),
                            'location' => $targetLocation
                        ]);
                    }
                }
                
                $maintenance->asset->save();
            } 
            // Jika Dibatalkan, kembalikan ke available (atau status sebelumnya jika ada logic advanced)
            // Jika Dibatalkan, tandai aset sebagai BROKEN (Rusak) karena belum diperbaiki
            elseif ($request->status === 'cancelled') {
                $maintenance->asset->update(['status' => 'broken']);
            }

            DB::commit();
            return redirect()->route('maintenances.index')->with('success', 'Status perbaikan diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }
}
