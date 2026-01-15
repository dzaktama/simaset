@extends('layouts.main')

@section('container')
<div class="mx-auto max-w-7xl px-4 py-8">
    
    {{-- 1. Header Dashboard --}}
    @include('dashboard.header')

    @if(auth()->user()->role === 'admin')
        {{-- === VIEW ADMIN === --}}
        
        {{-- 2. Statistik Cards --}}
        @include('dashboard.admin_stats')

        {{-- 3. Grafik & Chart --}}
        @include('dashboard.admin_charts')

        {{-- 4. Tabel Data --}}
        @include('dashboard.admin_tables')

        {{-- 5. Modals --}}
        @include('dashboard.modals')

    @else
        {{-- === VIEW USER === --}}
        @include('dashboard.user_view')
    @endif

</div>

{{-- 6. Scripts --}}
@include('dashboard.scripts')

@endsection