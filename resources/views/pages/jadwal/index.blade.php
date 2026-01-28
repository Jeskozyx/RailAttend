@extends('layouts.app')

@section('title')
    Data Jadwal
@endsection

@push('css')
@endpush



@section('content')
    <div class="min-h-screen py-2">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Data Jadwal</h1>
                        <p class="mt-1 text-sm text-gray-500">Kelola jadwal perjalanan kereta</p>
                    </div>
                    <a href="{{ route('schedule.create') }}"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tambah Jadwal</span>
                    </a>
                </div>
            </div>

            @include('components.alert.success')

            <!-- Search & Filter Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <form method="GET" action="{{ route('schedule.index') }}">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <!-- Search -->
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') ?? '' }}"
                                placeholder="Cari Kereta, Stasiun, atau No KA..."
                                class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        </div>

                        <!-- Sort Dropdown -->
                        <div class="flex items-center space-x-3">
                            <label class="text-sm font-medium text-gray-600 whitespace-nowrap">Tampilkan:</label>
                            <select name="sort" id="sort" onchange="this.form.submit()"
                                class="pl-4 pr-10 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-sm text-gray-700 transition-all duration-200">
                                <option value="10" {{ request('sort') == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('sort') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('sort') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('sort') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Trains Accordion List -->
            <div class="space-y-4">
                @forelse ($trains as $train)
                    <div x-data="{ expanded: false }"
                        class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                        <!-- Accordion Header -->
                        <button @click="expanded = !expanded"
                            class="w-full flex items-center justify-between px-6 py-5 bg-white hover:bg-gray-50 transition-colors duration-200 text-left">
                            <div class="flex items-center space-x-4">
                                <div class="bg-blue-100 p-2 rounded-lg text-blue-600">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2c-4 0-8 .5-8 4v9.5C4 17.43 5.57 19 7.5 19L6 20.5v.5h12v-.5L16.5 19c1.93 0 3.5-1.57 3.5-3.5V6c0-3.5-4-4-8-4zM7.5 17c-.83 0-1.5-.67-1.5-1.5S6.67 14 7.5 14s1.5.67 1.5 1.5S8.33 17 7.5 17zm3.5-6H6V6h5v5zm7 4.5c0 .83-.67 1.5-1.5 1.5s-1.5-.67-1.5-1.5.67-1.5 1.5-1.5 1.5.67 1.5 1.5zm0-4.5h-5V6h5v5z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $train->name }}</h3>
                                    <p class="text-sm text-gray-500">
                                        {{ $train->schedules->count() }} Jadwal Perjalanan
                                        @if (request('search') && $train->schedules->count() > 0)
                                            <span class="text-xs text-blue-600 ml-1">(Hasil Pencarian)</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="text-xs font-semibold px-3 py-1 bg-green-100 text-green-700 rounded-full">
                                    Aktif
                                </span>
                                <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200"
                                    :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </button>

                        <!-- Accordion Body -->
                        <div x-show="expanded" x-collapse style="display: none;">
                            <div class="p-6 bg-gray-50/50 border-t border-gray-100">
                                @if ($train->schedules->count() > 0)
                                    <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                        No KA / Trip</th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                        Rute Perjalanan</th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                        Waktu</th>

                                                    <th scope="col"
                                                        class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                        Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($train->schedules as $schedule)
                                                    <tr class="hover:bg-blue-50/30 transition-colors">
                                                        <!-- No KA & Tanggal -->
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="font-bold text-gray-900">{{ $schedule->no_ka }}
                                                            </div>
                                                            <div class="text-xs text-gray-500 mt-1 flex items-center">
                                                                <svg class="w-3 h-3 mr-1" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                                    </path>
                                                                </svg>
                                                                {{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d M Y') }}
                                                            </div>
                                                        </td>

                                                        <!-- Rute -->
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div
                                                                class="flex items-center text-sm font-medium text-gray-800">
                                                                <span>{{ $schedule->origin }}</span>
                                                                <svg class="w-4 h-4 mx-2 text-gray-400" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                                                </svg>
                                                                <span>{{ $schedule->destination }}</span>
                                                            </div>
                                                        </td>

                                                        <!-- Waktu -->
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm text-gray-900 font-medium">
                                                                {{ \Carbon\Carbon::parse($schedule->departure_time)->format('H:i') }}
                                                                -
                                                                {{ \Carbon\Carbon::parse($schedule->arrival_time)->format('H:i') }}
                                                            </div>
                                                            <div class="text-xs text-gray-500 mt-1">
                                                                @php
                                                                    $start = \Carbon\Carbon::parse(
                                                                        $schedule->departure_time,
                                                                    );
                                                                    $end = \Carbon\Carbon::parse(
                                                                        $schedule->arrival_time,
                                                                    );
                                                                    if ($end->lessThan($start)) {
                                                                        $end->addDay();
                                                                    }
                                                                    $diff = $start->diff($end);
                                                                @endphp
                                                                {{ $diff->format('%h Jam %i Mnt') }}
                                                            </div>
                                                        </td>

                                                        <!-- Status: REMOVED -->


                                                        <!-- Aksi -->
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                            <div class="flex items-center justify-end space-x-2">
                                                                <a href="{{ route('schedule.edit', ['id' => $schedule->id]) }}"
                                                                    class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition-colors border border-blue-200">
                                                                    <svg class="w-4 h-4" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                                    </svg>
                                                                </a>
                                                                <form
                                                                    action="{{ route('schedule.destroy', ['id' => $schedule->id]) }}"
                                                                    method="POST" class="delete-form inline-block">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="button"
                                                                        class="btn-delete text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition-colors border border-red-200">
                                                                        <svg class="w-4 h-4" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round" stroke-width="2"
                                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                        </svg>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <div
                                            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-sm font-semibold text-gray-900">Belum Ada Jadwal</h3>
                                        <p class="text-xs text-gray-500 mt-1">Belum ada jadwal perjalanan untuk kereta ini.
                                        </p>
                                        <a href="{{ route('schedule.create') }}"
                                            class="inline-block mt-4 text-xs font-semibold text-blue-600 hover:text-blue-800">
                                            + Tambah Jadwal Baru
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Tidak Ada Data Kereta</h3>
                        <p class="text-gray-500">
                            {{ request('search') ? 'Tidak ditemukan kereta dengan kata kunci "' . request('search') . '"' : 'Belum ada data kereta yang terdaftar.' }}
                        </p>
                    </div>
                @endforelse

                <!-- Pagination -->
                @if ($trains->hasPages())
                    <div class="mt-8">
                        {{ $trains->appends(request()->query())->links('pagination::tailwind') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('js')
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Yakin hapus?',
                        text: 'Data yang dihapus tidak bisa dikembalikan!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
