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
        $assets = Asset::where('status', '!=', 'maintenance')->get();

        return view('maintenances.create', [
            'title' => 'Input Perbaikan Baru',
            'assets' => $assets,
            'selectedAsset' => $asset
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
            'start_date' => 'required|date',
            'problem_description' => 'required|string',
            'cost' => 'nullable|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            // 1. Create Maintenance Log
            Maintenance::create([
                'asset_id' => $request->asset_id,
                'vendor_name' => $request->vendor_name,
                'start_date' => $request->start_date,
                'problem_description' => $request->problem_description,
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
            'completion_date' => 'required_if:status,completed|date|nullable',
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
