<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuideController extends Controller
{
    public function index()
    {
        return view('guides.index', [
            'title' => 'Panduan Sistem',
            'role' => auth()->user()->role->slug ?? 'user'
        ]);
    }
}
