<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RailAttend - Monitoring Kondektur</title>
    @vite('resources/css/app.css')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-slate-50">

    @include('layouts.navbar')

    <main class="max-w-7xl mx-auto px-6 py-12">
        <div class="mb-10 relative pl-6">
            <div class="absolute left-0 top-0 w-1.5 h-full bg-[#FF7300] rounded-full"></div>
            <h2 class="text-3xl font-black text-[#001D4B] tracking-tight">Daftar Kereta</h2>
            <p class="text-slate-500 font-medium text-sm mt-1">Pilih kereta untuk verifikasi keliling 30 menit.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach (['Taksaka', 'Argo Lawu', 'Argo Dwipangga', 'Fajar Utama'] as $namaKereta)
                <div
                    class="bg-white rounded-[2.5rem] border border-slate-100 shadow-[0_15px_40px_rgba(0,29,75,0.06)] overflow-hidden active:scale-[0.98] transition-transform duration-200">
                    <div class="p-5 flex flex-col items-center">

                        <div class="relative mb-2">
                            <div class="absolute inset-0 bg-[#001D4B] opacity-5 rounded-[2rem] scale-125 blur-xl"></div>
                            <div
                                class="relative w-20 h-20 bg-slate-50 rounded-[1.8rem] flex items-center justify-center border border-white shadow-inner">
                                <svg class="w-10 h-10 text-[#001D4B]" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2c-4 0-8 .5-8 4v9.5C4 17.43 5.57 19 7.5 19L6 20.5v.5h12v-.5L16.5 19c1.93 0 3.5-1.57 3.5-3.5V6c0-3.5-4-4-8-4zM7.5 17c-.83 0-1.5-.67-1.5-1.5S6.67 14 7.5 14s1.5.67 1.5 1.5S8.33 17 7.5 17zm3.5-6H6V6h5v5zm7 4.5c0 .83-.67 1.5-1.5 1.5s-1.5-.67-1.5-1.5.67-1.5 1.5-1.5 1.5.67 1.5 1.5zm0-4.5h-5V6h5v5z" />
                                </svg>
                            </div>
                        </div>

                        <div class="text-center mb-8">
                            <h3 class="text-2xl font-black text-[#001D4B] mt-1">{{ $namaKereta }}</h3>
                        </div>

                        <a href="{{ url('/jadwal') }}"
                            class="w-full bg-[#001D4B] active:bg-[#FF7300] text-white font-bold py-5 rounded-2xl shadow-lg flex items-center justify-center space-x-3 transition-colors duration-200">
                            <span class="tracking-widest text-sm">PILIH KERETA</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </main>

</body>

</html>
