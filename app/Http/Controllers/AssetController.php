<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    /**
     * Menampilkan Dashboard Utama (Statistics & Recent Assets)
     */
    public function index()
    {
        // 1. Ambil Statistik untuk Cards (Data Aggregation)
        $totalAssets = Asset::count();
        $availableAssets = Asset::where('status', 'available')->count();
        $deployedAssets = Asset::where('status', 'deployed')->count();
        $maintenanceAssets = Asset::whereIn('status', ['maintenance', 'broken'])->count();

        // 2. Ambil 5 Aset Terbaru (Recent Activity)
        // Kita gunakan 'with' untuk Eager Loading data holder agar query efisien (Performance Efficiency)
        $recentAssets = Asset::with('holder')->latest()->take(5)->get();

        return view('home', [
            'title' => 'Dashboard Aset IT',
            'totalAssets' => $totalAssets,
            'availableAssets' => $availableAssets,
            'deployedAssets' => $deployedAssets,
            'maintenanceAssets' => $maintenanceAssets,
            'recentAssets' => $recentAssets
        ]);
    }
}