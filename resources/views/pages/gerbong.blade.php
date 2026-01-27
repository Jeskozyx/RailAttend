@extends('layouts.app')

@section('title')
    Pilih Gerbong
@endsection

@push('css')
<style>
    .gerbong-card {
        background: linear-gradient(135deg, #001D4B 0%, #003D7A 50%, #0056b3 100%);
        position: relative;
        overflow: hidden;
    }
    
    .gerbong-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    
    .gerbong-card:hover::before {
        left: 100%;
    }
    
    .gerbong-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 30px -10px rgba(255, 115, 0, 0.4);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-6">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Title - Jadwal Kereta -->
        <div class="mb-4">
            <div class="flex items-center space-x-3 mb-2">
                <div class="h-1 w-10 bg-[#FF7300] rounded-full"></div>
                <span class="text-sm font-bold text-[#FF7300] uppercase tracking-wider">Jadwal Kereta</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-bold text-[#001D4B]">Informasi Perjalanan</h2>
        </div>

        <!-- Info Jadwal Kereta -->
        <div class="mb-8">
            <div class="w-full bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
                
                <!-- Header Card -->
                <div class="bg-gradient-to-r from-[#001D4B] to-[#003D7A] px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2c-4 0-8 .5-8 4v9.5C4 17.43 5.57 19 7.5 19L6 20.5v.5h12v-.5L16.5 19c1.93 0 3.5-1.57 3.5-3.5V6c0-3.5-4-4-8-4z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-bold text-lg">ARGO DWIPANGGA (15)</p>
                                <p class="text-blue-200 text-xs">27 Jan 2026</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-400 text-green-900">
                            Aktif
                        </span>
                    </div>
                </div>

                <!-- Route Info -->
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <!-- Departure -->
                        <div class="text-center">
                            <p class="text-xs text-slate-500 font-semibold mb-1">KEBERANGKATAN</p>
                            <p class="text-2xl sm:text-3xl font-bold text-[#001D4B] mb-1">SLO</p>
                            <p class="text-base sm:text-lg font-bold text-[#FF7300]">20:35</p>
                            <p class="text-xs text-slate-500">WIB</p>
                        </div>

                        <!-- Arrow -->
                        <div class="flex flex-col items-center px-4">
                            <svg class="w-10 h-10 sm:w-12 sm:h-12 text-[#FF7300]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                            <p class="text-xs text-slate-400 mt-1">7 jam 55 menit</p>
                        </div>

                        <!-- Arrival -->
                        <div class="text-center">
                            <p class="text-xs text-slate-500 font-semibold mb-1">KEDATANGAN</p>
                            <p class="text-2xl sm:text-3xl font-bold text-[#001D4B] mb-1">GMR</p>
                            <p class="text-base sm:text-lg font-bold text-[#FF7300]">03:30</p>
                            <p class="text-xs text-slate-500">WIB</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Header - Stanformasi -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="h-1 w-10 bg-[#001D4B] rounded-full"></div>
                        <span class="text-sm font-bold text-[#001D4B] uppercase tracking-wider">Stanformasi Gerbong</span>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-[#001D4B]">Pilih Gerbong Kereta</h3>
                    <p class="text-sm text-slate-500 mt-1">Terakhir Scan: <span class="font-semibold">-</span></p>
                </div>
                
                <!-- Quick Filter (Optional) -->
                <div class="hidden sm:flex items-center space-x-2">
                    <button class="px-4 py-2 bg-[#001D4B] rounded-lg text-white font-semibold text-sm hover:bg-[#003D7A] transition-all shadow-md">
                        Semua
                    </button>
                    <button class="px-4 py-2 bg-white rounded-lg border border-slate-300 text-slate-600 font-semibold text-sm hover:bg-slate-50 transition-all">
                        Luxury
                    </button>
                </div>
            </div>
        </div>

        <!-- Grid Gerbong (Warna Biru KAI) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            
            <!-- Gerbong Card 1 -->
            <a href="#" class="block group">
                <div class="gerbong-card rounded-2xl shadow-lg p-6 transition-all duration-300 relative">
                    <div class="relative z-10">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="px-3 py-1 bg-[#FF7300] rounded-full text-xs font-bold text-white">
                                        LUXURY
                                    </span>
                                </div>
                                <h4 class="text-2xl font-bold text-white mb-1">Gerbong 1</h4>
                                <p class="text-white/80 text-sm">Kelas Premium</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Stats -->
                        <div class="flex items-center space-x-4 pt-4 border-t border-white/20">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="text-white/90 text-sm font-semibold">-</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-white/90 text-sm font-semibold">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Gerbong Card 2 -->
            <a href="#" class="block group">
                <div class="gerbong-card rounded-2xl shadow-lg p-6 transition-all duration-300 relative">
                    <div class="relative z-10">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="px-3 py-1 bg-[#FF7300] rounded-full text-xs font-bold text-white">
                                        LUXURY
                                    </span>
                                </div>
                                <h4 class="text-2xl font-bold text-white mb-1">Gerbong 2</h4>
                                <p class="text-white/80 text-sm">Kelas Premium</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-4 pt-4 border-t border-white/20">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="text-white/90 text-sm font-semibold">-</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-white/90 text-sm font-semibold">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Gerbong Card 3 -->
            <a href="#" class="block group">
                <div class="gerbong-card rounded-2xl shadow-lg p-6 transition-all duration-300 relative">
                    <div class="relative z-10">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="px-3 py-1 bg-[#FF7300] rounded-full text-xs font-bold text-white">
                                        LUXURY
                                    </span>
                                </div>
                                <h4 class="text-2xl font-bold text-white mb-1">Gerbong 3</h4>
                                <p class="text-white/80 text-sm">Kelas Premium</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-4 pt-4 border-t border-white/20">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="text-white/90 text-sm font-semibold">-</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-white/90 text-sm font-semibold">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Gerbong Card 4 - Eksekutif -->
            <a href="#" class="block group">
                <div class="gerbong-card rounded-2xl shadow-lg p-6 transition-all duration-300 relative">
                    <div class="relative z-10">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="px-3 py-1 bg-[#FF7300] rounded-full text-xs font-bold text-white">
                                        EKSEKUTIF
                                    </span>
                                </div>
                                <h4 class="text-2xl font-bold text-white mb-1">Gerbong 1</h4>
                                <p class="text-white/80 text-sm">Kelas Eksekutif</p>
                            </div>
                            <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-4 pt-4 border-t border-white/20">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />