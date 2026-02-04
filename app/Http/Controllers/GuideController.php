<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guide;
use App\Models\GuideStep;

class GuideController extends Controller
{
    public function index()
    {
        $userRole = auth()->user()->role->slug ?? 'user';
        
        // Fetch from DB
        $allGuides = Guide::all();

        // Filter Logic
        $guides = $allGuides->filter(function ($guide) use ($userRole) {
            if ($userRole === 'super_admin') return true;
            return in_array('all', $guide->roles) || in_array($userRole, $guide->roles);
        });

        return view('guides.index', [
            'title' => 'Panduan Sistem',
            'role' => $userRole,
            'guides' => $guides
        ]);
    }

    public function show($id)
    {
        $guide = Guide::with('steps')->findOrFail($id);
        return view('guides.show', [
            'title' => $guide->title,
            'guide' => $guide
        ]);
    }

    // CREATE (Super Admin Only)
    public function create()
    {
        return view('guides.edit', ['guide' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'id' => 'required|unique:guides,id',
            'icon' => 'required',
            'color' => 'required',
            'steps.*.title' => 'required',
        ]);

        $guide = Guide::create([
            'id' => $request->id,
            'title' => $request->title,
            'description' => $request->description,
            'icon' => $request->icon,
            'color' => $request->color,
            'roles' => ['all'], // Default to all for simplicity now, or add input later
        ]);

        if ($request->has('steps')) {
            foreach ($request->steps as $index => $stepData) {
                $imagePath = null;
                if (isset($stepData['image_file']) && $stepData['image_file'] instanceof \Illuminate\Http\UploadedFile) {
                    $imagePath = $stepData['image_file']->store('guide-images', 'public');
                }

                GuideStep::create([
                    'guide_id' => $guide->id,
                    'title' => $stepData['title'],
                    'description' => $stepData['description'] ?? '',
                    'image' => $imagePath,
                    'order_index' => $index
                ]);
            }
        }

        return redirect()->route('guides.show', $guide->id)->with('success', 'Panduan berhasil dibuat');
    }

    // EDIT (Super Admin Only)
    public function edit($id)
    {
        $guide = Guide::with('steps')->findOrFail($id);
        return view('guides.edit', ['guide' => $guide]);
    }

    public function update(Request $request, $id)
    {
        $guide = Guide::findOrFail($id);
        
        $guide->update([
            'title' => $request->title,
            'description' => $request->description,
            'icon' => $request->icon,
            'color' => $request->color,
        ]);

        // Sync Steps
        // Strategy: Delete all old steps and re-create (simplest for reordering)
        // BUT need to preserve images.
        // Better: Update existing ones, delete removed ones.
        
        $existingStepIds = $guide->steps->pluck('id')->toArray();
        $submittedStepIds = [];

        if ($request->has('steps')) {
            foreach ($request->steps as $index => $stepData) {
                $stepId = $stepData['id'] ?? null;
                $imagePath = $stepData['image_path'] ?? null; // Old path

                if (isset($stepData['image_file']) && $stepData['image_file'] instanceof \Illuminate\Http\UploadedFile) {
                    $imagePath = $stepData['image_file']->store('guide-images', 'public');
                }

                if ($stepId) {
                    $step = GuideStep::find($stepId);
                    if ($step) {
                        $step->update([
                            'title' => $stepData['title'],
                            'description' => $stepData['description'] ?? '',
                            'image' => $imagePath,
                            'order_index' => $index
                        ]);
                        $submittedStepIds[] = $stepId;
                    }
                } else {
                    $newStep = GuideStep::create([
                        'guide_id' => $guide->id,
                        'title' => $stepData['title'],
                        'description' => $stepData['description'] ?? '',
                        'image' => $imagePath,
                        'order_index' => $index
                    ]);
                    $submittedStepIds[] = $newStep->id; // Not really needed for cleanup but consistent
                }
            }
        }

        // Delete steps that were removed in the form
        $stepsToDelete = array_diff($existingStepIds, $submittedStepIds);
        GuideStep::destroy($stepsToDelete);

        return redirect()->route('guides.show', $guide->id)->with('success', 'Panduan berhasil diperbarui');
    }

    // DELETE
    public function destroy($id)
    {
        Guide::findOrFail($id)->delete();
        return redirect()->route('guides.index')->with('success', 'Panduan berhasil dihapus');
    }
}
