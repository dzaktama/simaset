<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
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
        $asset = null;
        if ($request->has('asset_id')) {
            $asset = Asset::find($request->asset_id);
        }
        
        // Hanya aset yang statusnya 'available' atau 'broken' yang bisa diservis (Logic bebas)
        // Tapi sementara kita izinkan semua kecuali yang sedang maintenance
        $assets = Asset::with('holder')->where('status', '!=', 'maintenance')->get();

        return view('maintenances.create', [
            'title' => 'Input Perbaikan Baru',
            'assets' => $assets,
            'selectedAsset' => $asset
        ]);
    }

    /**
     * Display the specified maintenance record.
     */
    public function show(Maintenance $maintenance)
    {
        return view('maintenances.show', [
            'title' => 'Detail Perbaikan',
            'maintenance' => $maintenance->load('asset.holder')
        ]);
    }

    /**
     * Store a newly created maintenance record in storage.
     */
    public function store(Request $request)
    {
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
            // 1. Create Maintenance Log
            $description = $request->problem_description;
            if ($request->filled('priority')) {
                $priorityLabel = ucfirst($request->priority);
                $description = "[Prioritas: {$priorityLabel}] " . $description;
            }

            Maintenance::create([
                'asset_id' => $request->asset_id,
                'vendor_name' => $request->vendor_name,
                'start_date' => $request->start_date,
                'problem_description' => $description,
                'cost' => $request->cost ?? 0,
                'status' => 'on_process'
            ]);

            // 2. Update Asset Status
            $asset = Asset::find($request->asset_id);
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
        $request->validate([
            'status' => 'required|in:on_process,completed,cancelled',
            'vendor_name' => 'nullable|string',
            'start_date' => 'nullable|date',
            'problem_description' => 'nullable|string',
            'completion_date' => [
                'required_if:status,completed',
                'date',
                'nullable',
                function ($attribute, $value, $fail) use ($maintenance, $request) {
                    if ($value) {
                        $completion = \Carbon\Carbon::parse($value)->startOfDay();
                        // Use request start_date if present (editing), otherwise use existing
                        $startDateString = $request->start_date ?? $maintenance->start_date;
                        $start = \Carbon\Carbon::parse($startDateString)->startOfDay();
                        
                        if ($completion->lt($start)) {
                            $fail("Tanggal selesai ({$completion->format('d M Y')}) tidak boleh sebelum tanggal mulai service ({$start->format('d M Y')}).");
                        }
                    }
                }
            ],
            'cost' => 'nullable|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            $maintenance->update($request->all());

            // Jika Selesai, kembalikan status aset
            if ($request->status === 'completed') {
                $maintenance->asset->update(['status' => 'available']);
            } 
            // Jika Dibatalkan, kembalikan ke available (atau status sebelumnya jika ada logic advanced)
            elseif ($request->status === 'cancelled') {
                $maintenance->asset->update(['status' => 'available']);
            }

            DB::commit();
            return redirect()->route('maintenances.index')->with('success', 'Status perbaikan diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }
}
