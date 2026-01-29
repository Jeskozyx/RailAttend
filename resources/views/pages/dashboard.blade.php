@extends('layouts.app')

@section('title')
    Dashboard
@endsection

@push('css')
@endpush

@section('content')
    @role('Admin')
        <div class="min-h-screen py-2">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Dashboard Admin</h1>
                            <p class="mt-1 text-sm text-gray-500">Riwayat scan verifikasi per kereta</p>
                        </div>
                    </div>
                </div>

                <!-- Search Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <form method="GET" action="{{ route('dashboard') }}">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') ?? '' }}"
                                placeholder="Cari Nama Kereta..."
                                class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        </div>
                    </form>
                </div>

                <!-- Trains Accordion List -->
                <div class="space-y-4">
                    @forelse ($trains as $train)
                        @php
                            $reports = $train->scan_reports;
                            $totalReports = $reports->count();
                        @endphp
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
                                        <p class="text-sm text-gray-500">{{ $totalReports }} Laporan Dinasan</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <span
                                        class="text-xs font-semibold px-3 py-1 {{ $totalReports > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }} rounded-full">
                                        {{ $totalReports > 0 ? 'Ada Data' : 'Belum Ada' }}
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
                                    @if ($totalReports > 0)
                                        <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                            No</th>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                            Tanggal Jadwal</th>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                            Petugas</th>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                            Gerbong Discan</th>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                            Waktu Discan</th>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                            Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    @foreach ($reports->take(20) as $index => $report)
                                                        <tr class="hover:bg-blue-50/30 transition-colors">
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                {{ $index + 1 }}</td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <div class="text-sm font-medium text-gray-900">
                                                                    {{ $report->schedule ? \Carbon\Carbon::parse($report->schedule->date)->translatedFormat('d M Y') : '-' }}
                                                                </div>
                                                                <div class="text-xs text-gray-500">
                                                                    {{ $report->schedule->no_ka ?? '-' }}
                                                                </div>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <div class="text-sm font-medium text-gray-900">
                                                                    {{ $report->user->name ?? '-' }}</div>
                                                                <div class="text-xs text-gray-500">
                                                                    {{ $report->user->getRoleNames()->first() ?? 'Petugas' }}
                                                                </div>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <span
                                                                    class="px-2 py-1 bg-blue-100 text-blue-700 text-sm font-semibold rounded-full">
                                                                    {{ $report->verifications->count() }} Gerbong
                                                                </span>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <span
                                                                    class="px-2 py-1 bg-blue-100 text-blue-700 text-sm font-semibold rounded-full">
                                                                    {{ \Carbon\Carbon::parse($report->submitted_at)->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}
                                                                </span>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                @if ($report->status == 'completed')
                                                                    <span
                                                                        class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                                                        Selesai
                                                                    </span>
                                                                @else
                                                                    <span
                                                                        class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">
                                                                        Proses
                                                                    </span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @if ($totalReports > 20)
                                            <p class="text-xs text-gray-500 mt-3 text-center">Menampilkan 20 dari
                                                {{ $totalReports }} data terbaru</p>
                                        @endif
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
                                            <h3 class="text-sm font-semibold text-gray-900">Belum Ada Laporan</h3>
                                            <p class="text-xs text-gray-500 mt-1">Belum ada laporan dinasan untuk kereta ini.
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                </div>
            </div>
        </div>
    @else
        <div class="mb-12 text-center">
            <h2 class="text-4xl font-black text-[#001D4B] mb-3">Daftar Kereta</h2>
            <p class="text-slate-600 text-lg">Pilih kereta untuk verifikasi keliling 30 menit</p>
            <div class="w-24 h-1 bg-gradient-to-r from-transparent via-[#FF7300] to-transparent mx-auto mt-4"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($train as $kereta)
                <div
                    class="group bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">

                    <div class="relative h-40 bg-gradient-to-br from-[#001D4B] to-[#003D7A] overflow-hidden">
                        <div class="absolute inset-0 opacity-10">
                            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                                <path d="M0,0 Q50,20 100,0 L100,100 L0,100 Z" fill="white" />
                            </svg>
                        </div>

                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg class="w-20 h-20 text-white opacity-90 group-hover:scale-125 transition-transform duration-500"
                                fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2c-4 0-8 .5-8 4v9.5C4 17.43 5.57 19 7.5 19L6 20.5v.5h12v-.5L16.5 19c1.93 0 3.5-1.57 3.5-3.5V6c0-3.5-4-4-8-4zM7.5 17c-.83 0-1.5-.67-1.5-1.5S6.67 14 7.5 14s1.5.67 1.5 1.5S8.33 17 7.5 17zm3.5-6H6V6h5v5zm7 4.5c0 .83-.67 1.5-1.5 1.5s-1.5-.67-1.5-1.5.67-1.5 1.5-1.5 1.5.67 1.5 1.5zm0-4.5h-5V6h5v5z" />
                            </svg>
                        </div>

                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1 bg-white/90 backdrop-blur-sm rounded-full text-xs font-bold text-[#001D4B]">
                                {{ $loop->iteration }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-[#001D4B] mb-4 text-center">{{ $kereta->name }}</h3>

                        {{-- PERUBAHAN DI SINI: Menambahkan parameter train_id ke route --}}
                        <a href="{{ route('jadwal.view', ['train_id' => $kereta->id]) }}"
                            class="w-full bg-[#001D4B] hover:bg-[#FF7300] text-white font-bold py-4 rounded-xl flex items-center justify-center space-x-2 transition-all duration-300 group-hover:shadow-lg">
                            <span class="text-sm">PILIH KERETA</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endrole
@endsection


@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.5.0/chart.min.js"
        integrity="sha512-n/G+dROKbKL3GVngGWmWfwK0yPctjZQM752diVYnXZtD/48agpUKLIn0xDQL9ydZ91x6BiOmTIFwWjjFi2kEFg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush
