@if (!empty($rekap))
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: User Cards --}}
        <div class="lg:col-span-2 space-y-4">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <span class="w-1 h-5 bg-blue-600 rounded-full"></span>
                Rerata Jarak Waktu per User
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach ($userAverages ?? [] as $uid => $avg)
                    <div
                        class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold overflow-hidden">
                                    <img src="{{ $avg['avatar_url'] }}" alt="{{ $avg['user_name'] }}"
                                        class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 line-clamp-1">
                                        {{ $avg['user_name'] }}</p>
                                    <p class="text-[10px] uppercase font-bold text-blue-600 mb-0.5 tracking-wide">
                                        {{ $avg['user_jabatan'] ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $avg['jumlah_ronde'] }} Putaran • {{ $avg['jumlah_sesi'] }} Sesi
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="pl-13">
                            <p class="text-sm text-gray-500 mb-1">Rerata Jarak Waktu (Total)</p>
                            <p class="text-lg font-bold text-gray-900">{{ $avg['rerata_formatted'] }}</p>

                            {{-- Per-Schedule Breakdown --}}
                            @if (!empty($avg['schedules']) && count($avg['schedules']) > 0)
                                <div class="mt-3 pt-3 border-t border-gray-100 space-y-2">
                                    @foreach ($avg['schedules'] as $scheduleName => $sData)
                                        <div class="flex justify-between items-start text-xs py-1">
                                            <span
                                                class="text-gray-600 truncate max-w-[140px] mt-0.5 cursor-help border-b border-gray-300 border-dotted"
                                                title="{!! $sData['tooltip_text'] ?? $scheduleName !!}">{{ $scheduleName }}</span>
                                            <div class="text-right leading-tight">
                                                <div class="font-bold text-gray-900">{{ $sData['rerata_formatted'] }}
                                                </div>
                                                <div class="text-[10px] text-gray-500">{{ $sData['jumlah_ronde'] }}
                                                    Putaran</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Right Column: Total Summary --}}
        <div>
            <div
                class="bg-gradient-to-br from-[#001D4B] to-[#0a3d7c] rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
                {{-- Background Pattern --}}
                <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white/10 blur-2xl">
                </div>
                <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-24 h-24 rounded-full bg-blue-400/20 blur-xl">
                </div>

                <div class="relative z-10">
                    <h3 class="text-lg font-semibold text-blue-100 mb-6">Total Keseluruhan</h3>

                    <div class="space-y-6">
                        <div>
                            <p class="text-sm text-blue-200 mb-2">Total Rerata Jarak Waktu</p>
                            <div class="flex items-baseline gap-2">
                                <p class="text-4xl font-bold tracking-tight">
                                    {{ $rerataJarakWaktuFormatted ?? '0 Detik' }}</p>
                            </div>

                            @php
                                $jumlahUserValid = 0;
                                foreach ($userAverages ?? [] as $avg) {
                                    if ($avg['jumlah_ronde'] > $avg['jumlah_sesi']) {
                                        $jumlahUserValid++;
                                    }
                                }
                            @endphp
                            <p class="text-xs text-blue-300 mt-2">
                                *Dikalkulasi dari {{ $jumlahUserValid }} user yang aktif
                            </p>
                        </div>

                        <div class="pt-6 border-t border-white/10">
                            <div class="flex items-center justify-between">
                                <div class="text-center">
                                    <p class="text-2xl font-bold">{{ count($rekap) }}</p>
                                    <p class="text-xs text-blue-200 uppercase tracking-wider mt-1">Total
                                        Putaran</p>
                                </div>
                                <div class="w-px h-8 bg-white/20"></div>
                                {{-- <div class="text-center">
                                    <p class="text-2xl font-bold">{{ count($users) }}</p>
                                    <p class="text-xs text-blue-200 uppercase tracking-wider mt-1">Total
                                        User
                                    </p>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
