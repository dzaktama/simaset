@extends('layouts.main')

@section('title', $title ?? 'Dashboard')

@section('container')
<div class="mx-auto w-full px-4 py-8">
    
    {{-- Header Dashboard --}}
    @include('dashboard.header')

    @if(in_array(session('impersonate_role', optional(auth()->user()->role)->slug), ['admin', 'super_admin']))
        {{-- === VIEW ADMIN === --}}
        
        {{-- Statistik Cards --}}
        @include('dashboard.admin_stats')

        {{-- Grafik & Chart --}}
        @include('dashboard.admin_charts')

        {{-- Tabel Data --}}
        @include('dashboard.admin_tables')

        {{-- Modals --}}
        @include('dashboard.modals')

    @elseif(session('impersonate_role', optional(auth()->user()->role)->slug) == 'service_center')
        {{-- === VIEW SERVICE CENTER === --}}
        @include('dashboard.service_center_view')

    @else
        {{-- === VIEW USER === --}}
        {{-- File ini biasanya ada link ke 'assets.my' yang perlu diperbaiki --}}
        @include('dashboard.user_view') 
    @endif

</div>

{{-- Scripts --}}
@include('dashboard.scripts')

@endsection