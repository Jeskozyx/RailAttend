@extends('layouts.app')

@section('title')
    Pilih Gerbong - {{ $schedule->train->name }}
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
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .gerbong-card:hover::before {
            left: 100%;
        }

        .gerbong-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 30px -10px rgba(255, 115, 0, 0.4);
        }

        /* Animasi untuk Textarea */
        #incidentDescriptionContainer {
            transition: all 0.3s ease-in-out;
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }

        #incidentDescriptionContainer.show {
            max-height: 200px;
            opacity: 1;
            margin-top: 1rem;
        }
    </style>
@endpush

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-4">
                <div class="flex items-center space-x-3 mb-2">
                    <div class="h-1 w-10 bg-[#FF7300] rounded-full"></div>
                    <span class="text-sm font-bold text-[#FF7300] uppercase tracking-wider">Jadwal Kereta</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold text-[#001D4B]">Informasi Perjalanan</h2>
            </div>

            <div class="mb-8">
                <div class="w-full bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">

                    <div class="bg-gradient-to-r from-[#001D4B] to-[#003D7A] px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2c-4 0-8 .5-8 4v9.5C4 17.43 5.57 19 7.5 19L6 20.5v.5h12v-.5L16.5 19c1.93 0 3.5-1.57 3.5-3.5V6c0-3.5-4-4-8-4z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-white font-bold text-lg">{{ $schedule->train->name }}
                                        ({{ $schedule->no_ka }})</p>
                                    <p class="text-blue-200 text-xs">
                                        {{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d M Y') }}</p>
                                </div>
                            </div>
                            @php
                                $statusConfig = match ($schedule->status ?? 'pending') {
                                    'pending' => ['class' => 'bg-blue-400 text-blue-900', 'label' => 'Terjadwal'],
                                    'running' => ['class' => 'bg-green-400 text-green-900', 'label' => 'Aktif'],
                                    'completed' => ['class' => 'bg-gray-400 text-gray-900', 'label' => 'Selesai'],
                                    default => ['class' => 'bg-blue-400 text-blue-900', 'label' => 'Terjadwal'],
                                };
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusConfig['class'] }}">
                                {{ $statusConfig['label'] }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="text-center">
                                <p class="text-xs text-slate-500 font-semibold mb-1">KEBERANGKATAN</p>
                                <p class="text-2xl sm:text-3xl font-bold text-[#001D4B] mb-1">{{ $schedule->origin }}</p>
                                <p class="text-base sm:text-lg font-bold text-[#FF7300]">
                                    {{ \Carbon\Carbon::parse($schedule->departure_time)->format('H:i') }}</p>
                                <p class="text-xs text-slate-500">WIB</p>
                            </div>

                            <div class="flex flex-col items-center px-4">
                                <svg class="w-10 h-10 sm:w-12 sm:h-12 text-[#FF7300]" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                                @php
                                    $start = \Carbon\Carbon::parse($schedule->departure_time);
                                    $end = \Carbon\Carbon::parse($schedule->arrival_time);
                                    if ($end->lessThan($start)) {
                                        $end->addDay();
                                    }
                                    $diff = $start->diff($end);
                                @endphp
                                <p class="text-xs text-slate-400 mt-1">{{ $diff->format('%h jam %i menit') }}</p>
                            </div>

                            <div class="text-center">
                                <p class="text-xs text-slate-500 font-semibold mb-1">KEDATANGAN</p>
                                <p class="text-2xl sm:text-3xl font-bold text-[#001D4B] mb-1">{{ $schedule->destination }}
                                </p>
                                <p class="text-base sm:text-lg font-bold text-[#FF7300]">
                                    {{ \Carbon\Carbon::parse($schedule->arrival_time)->format('H:i') }}</p>
                                <p class="text-xs text-slate-500">WIB</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center space-x-3 mb-2">
                            <div class="h-1 w-10 bg-[#001D4B] rounded-full"></div>
                            <span class="text-sm font-bold text-[#001D4B] uppercase tracking-wider">Stanformasi
                                Gerbong</span>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-bold text-[#001D4B]">Pilih Gerbong Kereta</h3>
                        <p class="text-sm text-slate-500 mt-1">Total: <span
                                class="font-semibold text-[#FF7300]">{{ $schedule->train->rangkaians->count() }}
                                Gerbong</span></p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                @forelse ($schedule->train->rangkaians as $rangkaian)
                    @php
                        $typeConfig = match ($rangkaian->type) {
                            'LUX' => ['label' => 'LUXURY', 'desc' => 'Kelas Premium'],
                            'EKS' => ['label' => 'EKSEKUTIF', 'desc' => 'Kelas Eksekutif'],
                            'EKO' => ['label' => 'EKONOMI', 'desc' => 'Kelas Ekonomi'],
                            'KMP' => ['label' => 'KERETA MAKAN', 'desc' => 'Kereta Makan/Pembangkit'],
                            'BP' => ['label' => 'PEMBANGKIT', 'desc' => 'Kereta Pembangkit'],
                            default => ['label' => $rangkaian->type, 'desc' => 'Gerbong'],
                        };
                    @endphp
                    <a href="{{ route('kondektur.verifikasi', ['schedule_id' => $schedule->id, 'rangkaian_id' => $rangkaian->id]) }}"
                        class="block group cursor-pointer">
                        <div class="gerbong-card rounded-2xl shadow-lg p-6 transition-all duration-300 relative">
                            <div class="relative z-10">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <div class="flex items-center space-x-2 mb-2">
                                            <span class="px-3 py-1 bg-[#FF7300] rounded-full text-xs font-bold text-white">
                                                {{ $typeConfig['label'] }}
                                            </span>
                                            @if ($rangkaian->is_verified)
                                                <span
                                                    class="px-3 py-1 bg-green-500 rounded-full text-xs font-bold text-white flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="3" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Terverifikasi
                                                </span>
                                            @endif
                                        </div>
                                        <h4 class="text-2xl font-bold text-white mb-1">{{ $rangkaian->name }}</h4>
                                        <p class="text-white/80 text-sm">{{ $typeConfig['desc'] }}</p>
                                    </div>
                                    <div
                                        class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4 pt-4 border-t border-white/20">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-white/90 text-sm font-semibold">Urutan:
                                            {{ $rangkaian->urutan }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-2 text-center py-12 bg-white rounded-2xl border border-slate-200">
                        <div
                            class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-slate-500 font-bold mb-2">Belum ada gerbong untuk kereta ini.</p>
                        <p class="text-sm text-slate-400">Silakan tambahkan rangkaian Kereta melalui menu Jadwal.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8 pb-10">
                <form action="{{ route('kondektur.submit_report') }}" method="POST" id="submitReportForm">
                    @csrf
                    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">

                    <div class="bg-white rounded-2xl p-6 shadow-md border border-slate-200 mb-6">
                        <h4 class="text-lg font-bold text-[#001D4B] mb-4">Kondisi Perjalanan</h4>

                        <div class="flex flex-col sm:flex-row gap-4">
                            <label
                                class="relative flex items-center p-4 border rounded-xl cursor-pointer hover:bg-slate-50 transition-colors border-slate-200 has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                                <input type="radio" name="condition" value="safe"
                                    class="peer h-5 w-5 text-green-600 border-gray-300 focus:ring-green-500 condition-radio"
                                    checked>
                                <div class="ml-3">
                                    <span class="block text-sm font-bold text-slate-900 peer-checked:text-green-700">Aman /
                                        Terkendali</span>
                                    <span class="block text-xs text-slate-500">Tidak ada kendala selama perjalanan</span>
                                </div>
                            </label>

                            <label
                                class="relative flex items-center p-4 border rounded-xl cursor-pointer hover:bg-slate-50 transition-colors border-slate-200 has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                                <input type="radio" name="condition" value="incident"
                                    class="peer h-5 w-5 text-red-600 border-gray-300 focus:ring-red-500 condition-radio">
                                <div class="ml-3">
                                    <span class="block text-sm font-bold text-slate-900 peer-checked:text-red-700">Ada
                                        Insiden / Kendala</span>
                                    <span class="block text-xs text-slate-500">Terdapat masalah yang perlu
                                        dilaporkan</span>
                                </div>
                            </label>
                        </div>

                        <div id="incidentDescriptionContainer">
                            <label for="description" class="block text-sm font-medium text-slate-700 mb-2">Deskripsi
                                Insiden</label>
                            <textarea name="description" id="description" rows="3"
                                class="w-full rounded-xl border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                placeholder="Jelaskan secara singkat kendala atau insiden yang terjadi..."></textarea>
                        </div>
                    </div>

                    @php
                        $unverifiedCount = $schedule->train->rangkaians->where('is_verified', false)->count();
                    @endphp

                    <button type="submit"
                        class="w-full py-4 rounded-2xl font-black tracking-widest text-white transition-all shadow-lg
                    {{ $unverifiedCount > 0 ? 'bg-slate-300 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700 active:scale-95 shadow-indigo-200' }}"
                        {{ $unverifiedCount > 0 ? 'disabled' : '' }}>
                        KIRIM LAPORAN SCAN
                    </button>
                    @if ($unverifiedCount > 0)
                        <p class="text-center text-xs text-red-500 mt-2 font-semibold">
                            *Selesaikan scan seluruh gerbong ({{ $unverifiedCount }} tersisa) untuk mengirim laporan.
                        </p>
                    @endif
                </form>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const radios = document.querySelectorAll('.condition-radio');
                const container = document.getElementById('incidentDescriptionContainer');
                const textarea = document.getElementById('description');

                function toggleDescription() {
                    let isIncident = false;
                    radios.forEach(radio => {
                        if (radio.checked && radio.value === 'incident') {
                            isIncident = true;
                        }
                    });

                    if (isIncident) {
                        container.classList.add('show');
                        textarea.setAttribute('required', 'required');
                    } else {
                        container.classList.remove('show');
                        textarea.removeAttribute('required');
                        textarea.value = '';
                    }
                }

                radios.forEach(radio => {
                    radio.addEventListener('change', toggleDescription);
                });

                // Initial Check
                toggleDescription();
            });
        </script>
    @endpush
@endsection
