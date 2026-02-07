@extends('layouts.app')

@section('title')
    Rekap Waktu
@endsection

@section('content')
    @role('Admin')
        <div class="min-h-screen py-6 bg-gray-50/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                {{-- Header --}}
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Rekap Waktu</h1>
                    <p class="mt-1 text-sm text-gray-500">Detail waktu keliling per Putaran dengan rincian scan gerbong</p>
                </div>

                {{-- Filter Bar --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
                    <form action="" method="GET" class="flex flex-wrap items-center gap-4">
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-700">Dari:</label>
                            <input type="date" name="date_from" value="{{ $dateFrom ?? now()->format('Y-m-d') }}"
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-700">Sampai:</label>
                            <input type="date" name="date_to" value="{{ $dateTo ?? now()->format('Y-m-d') }}"
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-700">User:</label>
                            <select name="user_id"
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Semua User</option>
                                @foreach ($users ?? [] as $user)
                                    <option value="{{ $user->id }}" {{ ($userId ?? '') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->nipp ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-700">Jadwal:</label>
                            <select name="schedule_id"
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Semua Jadwal</option>
                                @foreach ($schedules ?? [] as $schedule)
                                    <option value="{{ $schedule->id }}"
                                        {{ ($scheduleId ?? '') == $schedule->id ? 'selected' : '' }}>
                                        🚂 {{ $schedule->train?->name ?? '-' }} ({{ $schedule->no_ka }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-700">Jabatan:</label>
                            <select name="role_id"
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Semua Jabatan</option>
                                @foreach ($roles ?? [] as $role)
                                    <option value="{{ $role->id }}" {{ ($roleId ?? '') == $role->id ? 'selected' : '' }}>
                                        👤 {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                            Filter
                        </button>
                    </form>
                </div>

                {{-- Table Card --}}
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-gradient-to-r from-[#001D4B] to-[#0a3d7c]">
                                    <th class="text-white font-semibold text-xs uppercase tracking-wider px-4 py-3.5 text-left rounded-tl-xl"
                                        style="width: 60px;"></th>
                                    <th class="text-white font-semibold text-xs uppercase tracking-wider px-4 py-3.5 text-left">
                                        Putaran</th>
                                    <th class="text-white font-semibold text-xs uppercase tracking-wider px-4 py-3.5 text-left">
                                        Tanggal</th>
                                    <th class="text-white font-semibold text-xs uppercase tracking-wider px-4 py-3.5 text-left">
                                        Nama User</th>
                                    <th class="text-white font-semibold text-xs uppercase tracking-wider px-4 py-3.5 text-left">
                                        NIPP</th>
                                    <th class="text-white font-semibold text-xs uppercase tracking-wider px-4 py-3.5 text-left">
                                        Jabatan</th>
                                    <th class="text-white font-semibold text-xs uppercase tracking-wider px-4 py-3.5 text-left">
                                        Waktu Awal</th>
                                    <th class="text-white font-semibold text-xs uppercase tracking-wider px-4 py-3.5 text-left">
                                        Waktu Akhir</th>
                                    <th class="text-white font-semibold text-xs uppercase tracking-wider px-4 py-3.5 text-left">
                                        Durasi</th>
                                    <th class="text-white font-semibold text-xs uppercase tracking-wider px-4 py-3.5 text-left">
                                        Jarak Waktu</th>
                                    <th class="text-white font-semibold text-xs uppercase tracking-wider px-4 py-3.5 text-left">
                                        Jumlah SF</th>
                                    <th
                                        class="text-white font-semibold text-xs uppercase tracking-wider px-4 py-3.5 text-left rounded-tr-xl">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rekap ?? [] as $index => $item)
                                    {{-- Parent Row --}}
                                    <tr class="parent-row bg-white hover:bg-gray-50 transition-colors cursor-pointer border-b border-gray-200"
                                        data-target="detail-{{ $index }}">
                                        <td class="px-4 py-3.5 align-middle">
                                            <button type="button"
                                                class="toggle-btn w-7 h-7 rounded-lg border-2 border-blue-500 bg-white text-blue-500 font-bold text-base inline-flex items-center justify-center hover:bg-blue-500 hover:text-white transition-all"
                                                onclick="toggleDetail({{ $index }}, event)">
                                                +
                                            </button>
                                        </td>
                                        <td class="px-4 py-3.5 align-middle">
                                            <span
                                                class="bg-gradient-to-br from-blue-500 to-blue-700 text-white w-8 h-8 rounded-lg inline-flex items-center justify-center font-bold text-sm">
                                                {{ $item['ronde'] ?? $loop->iteration }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 align-middle">
                                            <span class="text-gray-700 text-sm">{{ $item['tanggal'] ?? '-' }}</span>
                                        </td>
                                        <td class="px-4 py-3.5 align-middle">
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold text-xs">
                                                    {{ strtoupper(substr($item['user_name'] ?? 'U', 0, 1)) }}
                                                </div>
                                                <span class="font-medium text-gray-800">{{ $item['user_name'] ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3.5 align-middle">
                                            <span
                                                class="text-gray-700 font-mono text-sm">{{ $item['user_nipp'] ?? '-' }}</span>
                                        </td>
                                        <td class="px-4 py-3.5 align-middle">
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                                {{ $item['user_jabatan'] ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 align-middle">
                                            <span
                                                class="font-mono text-sm text-gray-900">{{ $item['waktu_awal'] ?? '-' }}</span>
                                        </td>
                                        <td class="px-4 py-3.5 align-middle">
                                            <span
                                                class="font-mono text-sm text-gray-900">{{ $item['waktu_akhir'] ?? '-' }}</span>
                                        </td>
                                        <td class="px-4 py-3.5 align-middle">
                                            @php
                                                $durasiDetik = $item['durasi_detik'] ?? 0;
                                                $isOverLimit = $durasiDetik > 1800;
                                                if ($durasiDetik < 60) {
                                                    $durasiFormatted = $durasiDetik . ' Detik';
                                                } elseif ($durasiDetik < 3600) {
                                                    $menit = floor($durasiDetik / 60);
                                                    $detik = $durasiDetik % 60;
                                                    $durasiFormatted =
                                                        $detik > 0
                                                            ? $menit . ' Menit ' . $detik . ' Detik'
                                                            : $menit . ' Menit';
                                                } else {
                                                    $jam = floor($durasiDetik / 3600);
                                                    $sisaDetik = $durasiDetik % 3600;
                                                    $menit = floor($sisaDetik / 60);
                                                    $detik = $sisaDetik % 60;
                                                    $durasiFormatted = $jam . ' Jam';
                                                    if ($menit > 0) {
                                                        $durasiFormatted .= ' ' . $menit . ' Menit';
                                                    }
                                                    if ($detik > 0) {
                                                        $durasiFormatted .= ' ' . $detik . ' Detik';
                                                    }
                                                }
                                            @endphp
                                            <span class="font-semibold {{ $isOverLimit ? 'text-red-600' : 'text-gray-900' }}">
                                                {{ $durasiFormatted }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 align-middle">
                                            @php
                                                $jarakDetik = $item['jarak_waktu_detik'] ?? 0;
                                                if ($jarakDetik < 60) {
                                                    $jarakFormatted = $jarakDetik . ' Detik';
                                                } elseif ($jarakDetik < 3600) {
                                                    $menit = floor($jarakDetik / 60);
                                                    $detik = $jarakDetik % 60;
                                                    $jarakFormatted =
                                                        $detik > 0
                                                            ? $menit . ' Menit ' . $detik . ' Detik'
                                                            : $menit . ' Menit';
                                                } else {
                                                    $jam = floor($jarakDetik / 3600);
                                                    $sisaDetik = $jarakDetik % 3600;
                                                    $menit = floor($sisaDetik / 60);
                                                    $detik = $sisaDetik % 60;
                                                    $jarakFormatted = $jam . ' Jam';
                                                    if ($menit > 0) {
                                                        $jarakFormatted .= ' ' . $menit . ' Menit';
                                                    }
                                                    if ($detik > 0) {
                                                        $jarakFormatted .= ' ' . $detik . ' Detik';
                                                    }
                                                }
                                            @endphp
                                            <span class="text-gray-600">{{ $jarakFormatted }}</span>
                                        </td>
                                        <td class="px-4 py-3.5 align-middle">
                                            <span
                                                class="inline-flex items-center gap-1.5 bg-indigo-100 text-indigo-800 px-2.5 py-1 rounded-lg font-semibold text-xs">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                                {{ count($item['details'] ?? []) }} Gerbong
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 align-middle">
                                            @php $status = $item['status'] ?? 'normal'; @endphp
                                            @if ($status === 'normal')
                                                <span
                                                    class="bg-green-100 text-green-800 px-2.5 py-1 rounded-full text-xs font-semibold">Normal</span>
                                            @elseif($status === 'warning')
                                                <span
                                                    class="bg-amber-100 text-amber-800 px-2.5 py-1 rounded-full text-xs font-semibold">Peringatan</span>
                                            @else
                                                <span
                                                    class="bg-red-100 text-red-800 px-2.5 py-1 rounded-full text-xs font-semibold">Melebihi
                                                    Batas</span>
                                            @endif
                                        </td>
                                    </tr>

                                    {{-- Child Row (Details) --}}
                                    <tr class="child-row hidden bg-gray-50" id="detail-{{ $index }}">
                                        <td colspan="12" class="px-4 pb-4 border-b border-gray-200">
                                            <div class="bg-white rounded-xl p-4 shadow-sm ml-11">
                                                <div class="flex items-center justify-between mb-4">
                                                    <p
                                                        class="text-xs font-semibold text-gray-500 uppercase tracking-wide flex items-center gap-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                        </svg>
                                                        Rincian Scan Gerbong - Putaran {{ $item['ronde'] ?? $loop->iteration }}
                                                    </p>
                                                    <div class="flex items-center gap-4">
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-xs text-gray-500">Kereta:</span>
                                                            <span
                                                                class="px-3 py-1 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-full text-xs font-semibold shadow-sm">
                                                                {{ $item['train_name'] ?? '-' }} - {{ $item['no_ka'] ?? '-' }}
                                                            </span>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-xs text-gray-500">Waktu Submit:</span>
                                                            <span
                                                                class="px-3 py-1 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-full text-xs font-semibold shadow-sm">
                                                                {{ $item['submitted_at'] ?? '-' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <table class="w-full">
                                                    <thead>
                                                        <tr class="bg-gray-100">
                                                            <th class="text-gray-600 font-semibold text-xs uppercase px-3 py-2.5 text-left rounded-l-lg"
                                                                style="width: 50px;">No</th>
                                                            <th
                                                                class="text-gray-600 font-semibold text-xs uppercase px-3 py-2.5 text-left">
                                                                Nama Gerbong</th>
                                                            <th
                                                                class="text-gray-600 font-semibold text-xs uppercase px-3 py-2.5 text-left rounded-r-lg">
                                                                Waktu Scan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-100">
                                                        @forelse ($item['details'] ?? [] as $detailIndex => $detail)
                                                            <tr class="hover:bg-gray-50">
                                                                <td class="px-3 py-2.5 text-sm text-gray-700">
                                                                    {{ $detailIndex + 1 }}</td>
                                                                <td class="px-3 py-2.5 text-sm font-medium text-gray-800">
                                                                    {{ $detail['nama_gerbong'] ?? '-' }}</td>
                                                                <td class="px-3 py-2.5 text-sm font-mono text-gray-900">
                                                                    {{ $detail['waktu_scan'] ?? '-' }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="3"
                                                                    class="text-center text-gray-400 py-4 text-sm">Tidak ada
                                                                    data scan</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center py-16">
                                            <div class="flex flex-col items-center">
                                                <div
                                                    class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </div>
                                                <p class="text-gray-500 font-medium">Tidak ada data rekap waktu</p>
                                                <p class="text-gray-400 text-sm mt-1">Pilih tanggal atau user untuk melihat
                                                    data</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    @else
        <script>
            window.location = "{{ route('dashboard') }}";
        </script>
    @endrole
@endsection

@push('js')
    <script>
        function toggleDetail(index, event) {
            event.stopPropagation();
            const childRow = document.getElementById(`detail-${index}`);
            const parentRow = document.querySelector(`[data-target="detail-${index}"]`);
            const toggleBtn = parentRow.querySelector('.toggle-btn');

            if (childRow.classList.contains('hidden')) {
                childRow.classList.remove('hidden');
                parentRow.classList.add('bg-gray-100');
                toggleBtn.classList.add('bg-blue-500', 'text-white', 'rotate-45');
                toggleBtn.classList.remove('bg-white', 'text-blue-500');
            } else {
                childRow.classList.add('hidden');
                parentRow.classList.remove('bg-gray-100');
                toggleBtn.classList.remove('bg-blue-500', 'text-white', 'rotate-45');
                toggleBtn.classList.add('bg-white', 'text-blue-500');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.parent-row').forEach(row => {
                row.addEventListener('click', function(e) {
                    if (e.target.closest('.toggle-btn')) return;
                    const target = this.getAttribute('data-target');
                    const index = target.replace('detail-', '');
                    toggleDetail(parseInt(index), e);
                });
            });
        });
    </script>
@endpush
