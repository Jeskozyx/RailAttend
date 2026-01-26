<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RailAttend - Pilih Jadwal Dinasan</title>
    @vite('resources/css/app.css')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-slate-50 font-sans antialiased text-[#001D4B]">

    <header class="bg-[#001D4B] text-white shadow-xl sticky top-0 z-50">
        <div class="max-w-full mx-auto px-6 lg:px-20 h-24 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="{{ url('/') }}"
                    class="bg-white p-2 rounded-xl shadow-md active:scale-95 transition-transform">
                    <img src="{{ asset('assets/images/kai_logo.png') }}" alt="KAI" class="h-8 w-auto">
                </a>
                <h1 class="text-xl font-black uppercase tracking-tighter">Pilih Jadwal</h1>
            </div>

            <div class="flex items-center space-x-3">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold leading-tight">Aulya Sab</p>
                    <p class="text-[10px] font-bold text-blue-300 uppercase">Kondektur</p>
                </div>
                <div
                    class="h-10 w-10 rounded-full bg-white flex items-center justify-center text-[#001D4B] font-black border-2 border-blue-400">
                    AS
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-6 py-10">
        <div class="mb-8 pl-4 border-l-4 border-[#FF7300]">
            <span class="text-xs font-black text-[#FF7300] uppercase tracking-widest">Dinasan Aktif</span>
            <h2 class="text-xl font-black tracking-tight">KA TAKSAKA (KA 67)</h2>
        </div>

        <h3 class="text-lg font-bold mb-6 flex items-center space-x-2">
            <svg class="w-5 h-5 text-[#FF7300]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>Daftar Jadwal Dinasan</span>
        </h3>

        <div class="space-y-4">
            @php
                $jadwalDinas = [
                    [
                        'ka' => 'TAKSKA (67)',
                        'asal' => 'YK',
                        'tujuan' => 'GMR',
                        'jam_asal' => '08:10',
                        'jam_tujuan' => '15:20',
                        'tgl' => '26 Jan 2026',
                    ],
                    [
                        'ka' => 'TAKSKA (68)',
                        'asal' => 'GMR',
                        'tujuan' => 'YK',
                        'jam_asal' => '21:00',
                        'jam_tujuan' => '03:40',
                        'tgl' => '27 Jan 2026',
                    ],
                ];
            @endphp

            @foreach ($jadwalDinas as $item)
                <button
                    class="w-full text-left bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden active:scale-[0.97] transition-all duration-200">
                    <div class="flex flex-col">
                        <div class="bg-[#FF7300] px-6 py-5 relative overflow-hidden">
                            <div class="absolute -right-4 -bottom-4 opacity-10">
                                <svg class="w-32 h-32 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2c-4 0-8 .5-8 4v9.5C4 17.43 5.57 19 7.5 19L6 20.5v.5h12v-.5L16.5 19c1.93 0 3.5-1.57 3.5-3.5V6c0-3.5-4-4-8-4z" />
                                </svg>
                            </div>

                            <div class="flex justify-between items-center text-white relative z-10">
                                <div class="flex flex-col">
                                    <span
                                        class="text-[10px] font-black uppercase tracking-widest opacity-80">Departure</span>
                                    <h4 class="text-3xl font-black tracking-tighter">{{ $item['asal'] }}</h4>
                                    <p class="text-xs font-bold">{{ $item['jam_asal'] }} WIB</p>
                                </div>

                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-[2px] bg-white/40 relative">
                                        <div
                                            class="absolute -top-1 left-1/2 -translate-x-1/2 w-2 h-2 rounded-full bg-white">
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col text-right">
                                    <span
                                        class="text-[10px] font-black uppercase tracking-widest opacity-80">Arrival</span>
                                    <h4 class="text-3xl font-black tracking-tighter">{{ $item['tujuan'] }}</h4>
                                    <p class="text-xs font-bold">{{ $item['jam_tujuan'] }} WIB</p>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 flex justify-between items-center bg-white">
                            <div class="flex flex-col">
                                <span
                                    class="text-[10px] font-black text-[#FF7300] uppercase tracking-widest">Kereta</span>
                                <p class="text-lg font-black">{{ $item['ka'] }}</p>
                            </div>
                            <div class="text-right">
                                <span
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal</span>
                                <p class="text-xs font-black">{{ $item['tgl'] }}</p>
                            </div>
                        </div>
                    </div>
                </button>
            @endforeach
        </div>
    </main>

    <a href="{{ url('/') }}"
        class="fixed bottom-8 left-1/2 -translate-x-1/2 bg-white/80 backdrop-blur-md px-6 py-3 rounded-full shadow-2xl border border-slate-200 text-sm font-black flex items-center space-x-2 active:scale-95 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>GANTI KERETA</span>
    </a>

</body>

</html>
