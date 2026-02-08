@forelse ($rekap ?? [] as $index => $item)
    {{-- Parent Row --}}
    <tr class="parent-row hover:bg-gray-50/80 transition-colors cursor-pointer group border-l-4 {{ $item['session_color'] ?? 'border-transparent' }}"
        data-target="detail-{{ $index }}">
        <td class="px-4 py-4 align-top">
            <button type="button"
                class="toggle-btn w-6 h-6 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center transition-all group-hover:bg-blue-50 group-hover:text-blue-500">
                <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </td>
        <td class="px-4 py-4 align-top">
            <span
                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-700 text-sm font-bold border border-blue-100">
                {{ $item['ronde'] }}
            </span>
        </td>
        <td class="px-4 py-4 align-top">
            <div class="flex items-start gap-3">
                <div
                    class="w-9 h-9 rounded-full bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center text-white font-bold text-xs shadow-sm overflow-hidden">
                    <img src="{{ $item['avatar_url'] }}" alt="{{ $item['user_name'] }}"
                        class="w-full h-full object-cover">
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900">{{ $item['user_name'] }}</p>
                    <p class="text-xs text-gray-500 font-mono">{{ $item['user_nipp'] }}</p>
                </div>
            </div>
        </td>
        <td class="px-4 py-4 align-top">
            <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                {{ $item['user_jabatan'] }}
            </span>
        </td>
        <td class="px-4 py-4 align-top">
            <div class="text-sm text-gray-900">
                <span class="font-medium">{{ $item['waktu_awal'] }}</span>
                <span class="text-gray-400 mx-1">→</span>
                <span class="font-medium">{{ $item['waktu_akhir'] }}</span>
            </div>
            <div class="text-xs text-gray-500 mt-0.5">{{ $item['tanggal'] }}</div>
        </td>
        <td class="px-4 py-4 align-top">
            @php $isOverLimit = ($item['durasi_detik'] ?? 0) > 1800; @endphp
            <span class="text-sm font-semibold {{ $isOverLimit ? 'text-red-600' : 'text-gray-900' }}">
                {{ $item['durasi_formatted'] }}
            </span>
        </td>
        <td class="px-4 py-4 align-top">
            <span class="text-sm text-gray-600">{{ $item['jarak_waktu_formatted'] }}</span>
        </td>
        <td class="px-4 py-4 align-top">
            <div class="flex flex-col gap-1">
                <div class="leading-tight">
                    <div class="text-sm font-bold text-gray-900">{{ $item['train_name'] ?? '-' }}
                        {{ $item['no_ka'] ?? '' }}</div>
                </div>
                <div>
                    <span
                        class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 border border-gray-200">
                        {{ count($item['details'] ?? []) }} SF
                    </span>
                </div>
            </div>
        </td>
        <td class="px-4 py-4 align-top">
            @php
                $statusColors = [
                    'normal' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                    'warning' => 'bg-amber-100 text-amber-800 border-amber-200',
                    'danger' => 'bg-red-100 text-red-800 border-red-200',
                ];
                $statusLabel = [
                    'normal' => 'Normal',
                    'warning' => 'Peringatan',
                    'danger' => 'Melebihi Batas',
                ];
                $status = $item['status'] ?? 'normal';
            @endphp
            <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $statusColors[$status] ?? $statusColors['normal'] }}">
                {{ $statusLabel[$status] ?? 'Normal' }}
            </span>
        </td>
    </tr>

    {{-- Child Row (Details) --}}
    <tr class="child-row hidden bg-gray-50/50 border-l-4 {{ $item['session_color'] ?? 'border-transparent' }}"
        id="detail-{{ $index }}">
        <td colspan="9" class="px-4 py-0 border-b border-gray-100">
            <div class="py-4 pl-12 pr-4">
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">Rincian
                                Scan - Putaran {{ $item['ronde'] }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-500">
                                Kereta: <span class="font-semibold text-gray-900">{{ $item['train_name'] }}
                                    ({{ $item['no_ka'] ?? '-' }})
                                </span>
                            </span>
                            <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                            <span class="text-xs text-gray-500">
                                Waktu Submit: <span class="font-mono text-gray-900">{{ $item['submitted_at'] }}</span>
                            </span>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-white">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase w-16">
                                        No</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                        Gerbong</th>
                                    <th
                                        class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase text-right">
                                        Waktu Scan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($item['details'] ?? [] as $detailIndex => $detail)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-sm text-gray-500">
                                            {{ $detailIndex + 1 }}</td>
                                        <td class="px-4 py-2 text-sm font-medium text-gray-900">
                                            {{ $detail['nama_gerbong'] }}</td>
                                        <td class="px-4 py-2 text-sm font-mono text-gray-600 text-right">
                                            {{ $detail['waktu_scan'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-8 text-center text-gray-400 text-sm">
                                            Tidak ada data scan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="px-4 py-16 text-center">
            <div class="flex flex-col items-center justify-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Tidak ada data ditemukan</h3>
                <p class="text-sm text-gray-500 mt-1">Coba ubah filter pencarian Anda</p>
            </div>
        </td>
    </tr>
@endforelse
