@extends('layouts.app')

@section('title', 'Jadwal Dinasan Kondektur')

@push('css')
@endpush

@section('content')
    <div class="min-h-screen bg-[#F8FAFC] pb-20">
        <!-- Header Section -->
        <div class="mb-6 sm:mb-8 px-4 sm:px-5">
            <div class="flex items-center space-x-2 sm:space-x-3 mb-2">
                <div class="h-1 w-8 sm:w-10 bg-[#FF7300] rounded-full"></div>
                <span class="text-xs sm:text-sm font-bold text-[#FF7300] uppercase tracking-wider">Dinasan Aktif</span>
            </div>
            @if ($jadwalDinas->isNotEmpty())
                <h2 class="text-4xl sm:text-6xl font-bold text-[#001D4B]">
                    {{ $title }}
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Petugas: {{ Auth::user()->name }}</p>
            @else
                <h2 class="text-xl sm:text-2xl font-bold text-[#001D4B]">
                    Petugas: {{ Auth::user()->name }}
                </h2>
            @endif
        </div>

        <div class="px-4 sm:px-5">
            <!-- Section Title -->
            <div class="mb-4 sm:mb-6 flex items-center space-x-2 sm:space-x-3">
                <div
                    class="w-8 h-8 sm:w-10 sm:h-10 bg-[#FF7300]/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#FF7300]" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-base sm:text-lg font-bold text-[#001D4B]">Daftar Jadwal Dinasan</h3>
            </div>

            <!-- Schedule Cards -->
            <div class="space-y-3 sm:space-y-4">
                @forelse ($jadwalDinas as $item)
                    <a href="{{ url('/gerbong/' . $item->id) }}"
                        class="block w-full text-left bg-white rounded-xl sm:rounded-2xl shadow-md hover:shadow-xl border border-slate-200 hover:border-[#FF7300] overflow-hidden transition-all duration-300 active:scale-[0.98]">

                        <!-- Header Card -->
                        <div class="bg-gradient-to-r from-[#001D4B] to-[#003D7A] px-4 sm:px-6 py-3 sm:py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2 sm:space-x-3 min-w-0 flex-1">
                                    <div
                                        class="w-8 h-8 sm:w-10 sm:h-10 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M12 2c-4 0-8 .5-8 4v9.5C4 17.43 5.57 19 7.5 19L6 20.5v.5h12v-.5L16.5 19c1.93 0 3.5-1.57 3.5-3.5V6c0-3.5-4-4-8-4z" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-white font-bold text-sm sm:text-lg truncate">{{ $item->train->name }}
                                            ({{ $item->no_ka }})
                                        </p>
                                        <p class="text-blue-200 text-[10px] sm:text-xs">
                                            {{ \Carbon\Carbon::parse($item->date)->translatedFormat('d M Y') }}
                                        </p>
                                    </div>
                                </div>
                                @php
                                    $statusConfig = match ($item->status) {
                                        'pending' => ['class' => 'bg-blue-400 text-blue-900', 'label' => 'Terjadwal'],
                                        'running' => ['class' => 'bg-green-400 text-green-900', 'label' => 'Aktif'],
                                        'completed' => ['class' => 'bg-gray-400 text-gray-900', 'label' => 'Selesai'],
                                        default => ['class' => 'bg-blue-400 text-blue-900', 'label' => 'Terjadwal'],
                                    };
                                @endphp
                                <span
                                    class="px-2 sm:px-3 py-1 rounded-full text-[10px] sm:text-xs font-bold flex-shrink-0 {{ $statusConfig['class'] }}">
                                    {{ $statusConfig['label'] }}
                                </span>
                            </div>
                        </div>

                        <!-- Route Info -->
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center justify-between">
                                <!-- Departure -->
                                <div class="text-center flex-1 min-w-0">
                                    <p class="text-[9px] sm:text-xs text-slate-500 font-semibold mb-0.5 sm:mb-1">
                                        KEBERANGKATAN</p>
                                    <p class="text-xl sm:text-3xl font-bold text-[#001D4B] mb-0.5 sm:mb-1 truncate">
                                        {{ $item->origin }}</p>
                                    <p class="text-sm sm:text-lg font-bold text-[#FF7300]">
                                        {{ \Carbon\Carbon::parse($item->departure_time)->format('H:i') }}
                                    </p>
                                    <p class="text-[9px] sm:text-xs text-slate-500">WIB</p>
                                </div>

                                <!-- Arrow -->
                                <div class="flex flex-col items-center px-2 sm:px-4 flex-shrink-0">
                                    <svg class="w-8 h-8 sm:w-12 sm:h-12 text-[#FF7300]" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                    <p class="text-xs text-slate-400 mt-1"> @php
                                        $start = \Carbon\Carbon::parse($item->departure_time);
                                        $end = \Carbon\Carbon::parse($item->arrival_time);
                                        if ($end->lessThan($start)) {
                                            $end->addDay();
                                        }
                                        $diff = $start->diff($end);
                                    @endphp
                                        {{ $diff->format('%h Jam %i Mnt') }}</p>
                                </div>

                                <!-- Arrival -->
                                <div class="text-center flex-1 min-w-0">
                                    <p class="text-[9px] sm:text-xs text-slate-500 font-semibold mb-0.5 sm:mb-1">KEDATANGAN
                                    </p>
                                    <p class="text-xl sm:text-3xl font-bold text-[#001D4B] mb-0.5 sm:mb-1 truncate">
                                        {{ $item->destination }}</p>
                                    <p class="text-sm sm:text-lg font-bold text-[#FF7300]">
                                        {{ \Carbon\Carbon::parse($item->arrival_time)->format('H:i') }}
                                    </p>
                                    <p class="text-[9px] sm:text-xs text-slate-500">WIB</p>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div
                            class="bg-slate-50 px-4 sm:px-6 py-2.5 sm:py-3 border-t border-slate-200 flex items-center justify-between">
                            <span href="{{ route('gerbong.show', $item->id) }}"
                                class="text-xs sm:text-sm font-semibold text-slate-600">Lihat Detail</span>
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#FF7300] flex-shrink-0" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-12 sm:py-20">
                        <div
                            class="w-16 h-16 sm:w-20 sm:h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4 text-slate-300">
                            <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-sm sm:text-base text-slate-500 font-bold px-4">Tidak ada jadwal dinasan hari ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('js')
@endpush
