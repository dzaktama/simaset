<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetRequest;
use App\Models\AssetHistory;
use App\Services\AssetService;
use Illuminate\Http\Request;

class AssetRequestController extends Controller
{
    private AssetService $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }
    /**
     * [KARYAWAN] Mengajukan Peminjaman
     * Single entry point untuk request aset
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'quantity' => 'required|integer|min:1', 
            'return_date' => 'nullable|date|after_or_equal:today',
            'return_time' => 'nullable', 
            'reason' => 'required|string|max:255',
            'is_booking' => 'nullable|boolean',
        ]);

        try {
            $this->assetService->createAssetRequest(
                userId: auth()->id(),
                assetId: $validatedData['asset_id'],
                quantity: $validatedData['quantity'],
                returnDate: $validatedData['return_date'] ?? null,
                returnTime: $validatedData['return_time'] ?? null,
                reason: $validatedData['reason'],
                isBooking: $validatedData['is_booking'] ?? false
            );

            $msg = $validatedData['is_booking'] ?? false
                ? 'Berhasil Booking! Menunggu aset dikembalikan & persetujuan Admin.'
                : 'Permintaan berhasil dikirim! Menunggu persetujuan Admin.';

            return back()->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * [ADMIN] Menyetujui Peminjaman (Stok Berkurang)
     */
    public function approve($id)
    {
        $assetRequest = AssetRequest::findOrFail($id);

        try {
            $this->assetService->approveAssetRequest($assetRequest);
            return back()->with('success', 'Permintaan disetujui. Stok aset telah dikurangi.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * [ADMIN] Menolak Peminjaman
     */
    public function reject(Request $req, $id)
    {
        $assetRequest = AssetRequest::findOrFail($id);

        $req->validate(['admin_note' => 'required|string|max:255']);

        try {
            $this->assetService->rejectAssetRequest($assetRequest, $req->admin_note);
            return back()->with('success', 'Permintaan berhasil ditolak.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}