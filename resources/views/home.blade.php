@extends('layouts.main')

@section('container')
<div class="mx-auto max-w-7xl px-4 py-8">
    
    {{-- Header Dashboard --}}
    @include('dashboard.header')

    @if(auth()->user()->role === 'admin')
        {{-- === VIEW ADMIN === --}}
        
        {{-- Statistik Cards --}}
        @include('dashboard.admin_stats')

        {{-- Grafik & Chart --}}
        @include('dashboard.admin_charts')

        {{-- Tabel Data --}}
        @include('dashboard.admin_tables')

        {{-- Modals --}}
        @include('dashboard.modals')

    @else
        {{-- === VIEW USER === --}}
        {{-- File ini biasanya ada link ke 'assets.my' yang perlu diperbaiki --}}
        @include('dashboard.user_view') 
    @endif

</div>

{{-- Scripts --}}
@include('dashboard.scripts')

@endsection