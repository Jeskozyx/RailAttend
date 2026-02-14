@forelse ($rekap ?? [] as $index => $session)
    {{-- Session Row (Parent) --}}
    @php
        $sessionId = 'session-' . $session['session_id'];
    @endphp
    <tr class="parent-row hover:bg-gray-50/80 transition-colors cursor-pointer group border-l-4"
        style="border-left-color: {{ $session['session_color'] ?? 'transparent' }};"
        data-target="detail-{{ $sessionId }}">
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
                {{ $session['sesi_ke'] }}
            </span>
        </td>
        <td class="px-4 py-4 align-top">
            <div class="flex items-start gap-3">
                <div
                    class="w-9 h-9 rounded-full bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center text-white font-bold text-xs shadow-sm overflow-hidden">
                    <img src="{{ $session['avatar_url'] }}" alt="{{ $session['user_name'] }}"
                        class="w-full h-full object-cover">
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900">{{ $session['user_name'] }}</p>
                    <p class="text-xs text-gray-500 font-mono">{{ $session['user_nipp'] }}</p>
                </div>
            </div>
        </td>
        <td class="px-4 py-4 align-top">
            <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                {{ $session['user_jabatan'] }}
            </span>
        </td>
        <td class="px-4 py-4 align-top">
            <div class="text-sm text-gray-900">
                <span class="font-medium">{{ $session['waktu_awal'] }}</span>
                <span class="text-gray-400 mx-1">→</span>
                <span class="font-medium">{{ $session['waktu_akhir'] }}</span>
            </div>
            <div class="text-xs text-gray-500 mt-0.5">{{ $session['tanggal'] }}</div>
        </td>
        <td class="px-4 py-4 align-top">
            <span class="text-sm font-semibold text-gray-900">
                {{ $session['durasi_sesi'] }}
            </span>
        </td>
        <td class="px-4 py-4 align-top">
            <span class="text-sm text-gray-600">{{ $session['rerata_gap'] }}</span>
        </td>
        <td class="px-4 py-4 align-top">
            <div class="flex flex-col gap-1">
                <div class="leading-tight">
                    <div class="text-sm font-bold text-gray-900">{{ $session['train_name'] ?? '-' }}
                        {{ $session['no_ka'] ?? '' }}</div>
                </div>
                <div class="text-xs text-gray-500">
                    {{ $session['schedule_info'] }}
                </div>
            </div>
        </td>
        <td class="px-4 py-4 align-top">
            <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-800 border border-gray-200">
                {{ $session['jumlah_putaran'] }} Putaran
            </span>
        </td>
    </tr>

    {{-- Session Details (Child Row) --}}
    <tr class="child-row hidden bg-gray-50/50 border-l-4"
        style="border-left-color: {{ $session['session_color'] ?? 'transparent' }};" id="detail-{{ $sessionId }}">
        <td colspan="9" class="px-0 py-0 border-b border-gray-100">
            <div class="py-4 pl-12 pr-4">
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                        <h4 class="text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Rincian Putaran - Sesi {{ $session['sesi_ke'] }}
                        </h4>
                    </div>

                    {{-- Nested Table for Rounds --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 text-xs uppercase font-medium text-gray-500">
                                <tr>
                                    <th class="px-4 py-2 w-8"></th>
                                    <th class="px-4 py-2">Putaran</th>
                                    <th class="px-4 py-2">Waktu</th>
                                    <th class="px-4 py-2">Durasi</th>
                                    <th class="px-4 py-2">Jarak Waktu </th>
                                    <th class="px-4 py-2">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($session['rounds'] as $round)
                                    @php
                                        $roundId = 'round-' . $round['id'];
                                    @endphp
                                    {{-- Round Row --}}
                                    <tr class="parent-row hover:bg-blue-50 transition-colors cursor-pointer"
                                        data-target="detail-{{ $roundId }}">
                                        <td class="px-4 py-2">
                                            <button type="button"
                                                class="toggle-btn w-5 h-5 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center hover:bg-blue-200 hover:text-blue-700">
                                                <svg class="w-3 h-3 transform transition-transform duration-200"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </button>
                                        </td>
                                        <td class="px-4 py-2">
                                            <span class="font-bold text-gray-700">{{ $round['ronde'] }}</span>
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-600">
                                            {{ $round['waktu_awal'] }} - {{ $round['waktu_akhir'] }}
                                        </td>
                                        <td class="px-4 py-2 text-sm">
                                            @php $isOverLimit = ($round['durasi_detik'] ?? 0) > 1800; @endphp
                                            <span
                                                class="{{ $isOverLimit ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                                                {{ $round['durasi_formatted'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-sm">
                                            @if (($round['jarak_waktu_detik'] ?? 0) == 0)
                                                <span
                                                    class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700">Awal
                                                    Putaran</span>
                                            @else
                                                {{ $round['jarak_waktu_formatted'] }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">
                                            @php
                                                $statusColors = [
                                                    'normal' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                    'warning' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                    'danger' => 'bg-red-100 text-red-800 border-red-200',
                                                ];
                                                $status = $round['status'] ?? 'normal';
                                            @endphp
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold border {{ $statusColors[$status] ?? $statusColors['normal'] }}">
                                                {{ ucfirst($status) }}
                                            </span>
                                        </td>
                                    </tr>

                                    {{-- Round Details (Scans) --}}
                                    <tr class="child-row hidden bg-slate-50" id="detail-{{ $roundId }}">
                                        <td colspan="6" class="px-4 py-3 border-t border-gray-100 shadow-inner">
                                            <div class="ml-8">
                                                <div class="mb-2 flex items-center gap-2 text-xs text-gray-500">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                    </svg>
                                                    <span class="font-semibold text-gray-700">Rincian Scan
                                                        Gerbong</span>
                                                    <span class="mx-1">•</span>
                                                    <span>{{ $round['train_name'] }}</span>
                                                    <span class="text-gray-400">|</span>
                                                    <span>{{ count($round['details']) }} SF</span>
                                                </div>
                                                <div class="flex flex-wrap gap-4 items-center">
                                                    @foreach ($round['details'] as $scan)
                                                        {{-- Gerbong Card --}}
                                                        <div
                                                            class="bg-white border border-gray-200 rounded p-2 text-center shadow-sm min-w-[80px]">
                                                            <div class="text-xs font-bold text-gray-800">
                                                                {{ $scan['nama_gerbong'] }}</div>
                                                            <div class="text-[10px] text-blue-600 font-mono mt-1">
                                                                {{ $scan['waktu_scan'] }}</div>
                                                        </div>

                                                        {{-- Arrow with Gap (if gap_to_next exists) --}}
                                                        @if ($scan['gap_to_next'])
                                                            <div
                                                                class="flex flex-col items-center justify-center -mx-2">
                                                                <span
                                                                    class="text-[9px] text-gray-500 font-mono mb-0.5">{{ $scan['gap_to_next'] }}</span>
                                                                <svg class="w-4 h-4 text-gray-300 transform"
                                                                    fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                                </svg>
                                                            </div>
                                                        @endif
                                                    @endforeach

                                                    {{-- Final Submit Time --}}
                                                    @if (isset($round['waktu_submit']))
                                                        <div class="flex flex-col items-center justify-center -mx-2">
                                                            <svg class="w-4 h-4 text-gray-300 transform"
                                                                fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                            </svg>
                                                        </div>

                                                        <div
                                                            class="bg-blue-50 border border-blue-200 rounded p-2 text-center shadow-sm min-w-[80px]">
                                                            <div class="text-xs font-bold text-blue-800">
                                                                Submit
                                                            </div>
                                                            <div class="text-[10px] text-blue-600 font-mono mt-1">
                                                                {{ $round['waktu_submit'] }}
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                @if (empty($round['details']))
                                                    <span class="text-xs text-gray-400 italic">Tidak ada data
                                                        scan</span>
                                                @endif
                                            </div>
                    </div>
        </td>
    </tr>
@endforeach
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
