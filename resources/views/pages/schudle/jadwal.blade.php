@extends('layouts.app')

@section('title')
    Jadwal Keberangkatan Kereta
@endsection

@push('css')
    
@endpush

@section('content')
    <!-- Header Section -->
<div class="mb-8">
    <div class="flex items-center space-x-3 mb-2">
        <div class="h-1 w-10 bg-[#FF7300] rounded-full"></div>
        <span class="text-sm font-bold text-[#FF7300] uppercase tracking-wider">Dinasan Aktif</span>
    </div>
    <h2 class="text-2xl font-bold text-[#001D4B]">KA TAKSAKA (KA 67)</h2>
</div>

<!-- Section Title -->
<div class="mb-6 flex items-center space-x-3">
    <div class="w-10 h-10 bg-[#FF7300]/10 rounded-xl flex items-center justify-center">
        <svg class="w-5 h-5 text-[#FF7300]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
    </div>
    <h3 class="text-lg font-bold text-[#001D4B]">Daftar Jadwal Dinasan</h3>
</div>

<!-- Schedule Cards -->
<div class="space-y-4">
    @php
        $jadwalDinas = [
            [
                'ka' => 'TAKSAKA (67)',
                'asal' => 'YK',
                'tujuan' => 'GMR',
                'jam_asal' => '08:10',
                'jam_tujuan' => '15:20',
                'tgl' => '26 Jan 2026',
                'status' => 'Aktif',
            ],
            [
                'ka' => 'TAKSAKA (68)',
                'asal' => 'GMR',
                'tujuan' => 'YK',
                'jam_asal' => '21:00',
                'jam_tujuan' => '03:40',
                'tgl' => '27 Jan 2026',
                'status' => 'Terjadwal',
            ],
        ];
    @endphp

    @foreach ($jadwalDinas as $item)
        <button
            class="w-full text-left bg-white rounded-2xl shadow-md hover:shadow-xl border border-slate-200 hover:border-[#FF7300] overflow-hidden transition-all duration-300">
            
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
                            <p class="text-white font-bold text-lg">{{ $item['ka'] }}</p>
                            <p class="text-blue-200 text-xs">{{ $item['tgl'] }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold
                        {{ $item['status'] === 'Aktif' ? 'bg-green-400 text-green-900' : 'bg-blue-400 text-blue-900' }}">
                        {{ $item['status'] }}
                    </span>
                </div>
            </div>

            <!-- Route Info -->
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <!-- Departure -->
                    <div class="text-center">
                        <p class="text-xs text-slate-500 font-semibold mb-1">KEBERANGKATAN</p>
                        <p class="text-3xl font-bold text-[#001D4B] mb-1">{{ $item['asal'] }}</p>
                        <p class="text-lg font-bold text-[#FF7300]">{{ $item['jam_asal'] }}</p>
                        <p class="text-xs text-slate-500">WIB</p>
                    </div>

                    <!-- Arrow -->
                    <div class="flex flex-col items-center px-4">
                        <svg class="w-12 h-12 text-[#FF7300]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </div>

                    <!-- Arrival -->
                    <div class="text-center">
                        <p class="text-xs text-slate-500 font-semibold mb-1">KEDATANGAN</p>
                        <p class="text-3xl font-bold text-[#001D4B] mb-1">{{ $item['tujuan'] }}</p>
                        <p class="text-lg font-bold text-[#FF7300]">{{ $item['jam_tujuan'] }}</p>
                        <p class="text-xs text-slate-500">WIB</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-slate-50 px-6 py-3 border-t border-slate-200 flex items-center justify-between">
                <span class="text-sm font-semibold text-slate-600">Lihat Detail</span>
                <svg class="w-5 h-5 text-[#FF7300]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </button>
    @endforeach
</div>
@endsection

@push('js')
    
@endpush